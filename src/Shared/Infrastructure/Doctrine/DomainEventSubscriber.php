<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Entity\Aggregate;
use App\Shared\Domain\Specification\SpecificationInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

final class DomainEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var array<Aggregate>
     */
    private array $entities = [];

    private EventBusInterface $eventBus;
    private ContainerBagInterface $container;

    public function __construct(EventBusInterface $eventBus, ContainerBagInterface $containerBag)
    {
        $this->eventBus = $eventBus;
        $this->container = $containerBag;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postFlush,
            Events::postLoad,
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->keepAggregateRoots($args);
    }

    private function keepAggregateRoots(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!($entity instanceof Aggregate)) {
            return;
        }

        $this->entities[] = $entity;
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->keepAggregateRoots($args);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->keepAggregateRoots($args);
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        foreach ($this->entities as $entity) {
            foreach ($entity->popEvents() as $event) {
                $this->eventBus->execute($event);
            }
        }
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        $reflect = new ReflectionClass($entity);

        foreach ($reflect->getProperties() as $property) {
            $type = $property->getType();

            if (is_null($type) || $type->isBuiltin() || $property->isInitialized($entity)) {
                continue;
            }

            // initialize specifications
            $interfaces = class_implements($property->getType()->getName());
            if (isset($interfaces[SpecificationInterface::class])) {
                $property->setValue($entity, $this->container->get($property->getType()->getName()));
            }
        }
    }
}
