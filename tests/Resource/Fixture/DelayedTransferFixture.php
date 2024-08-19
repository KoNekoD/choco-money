<?php

declare(strict_types=1);

namespace App\Tests\Resource\Fixture;

use App\Tests\Tools\FakerTools;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DelayedTransferFixture
    extends Fixture
    //implements DependentFixtureInterface
{
    use FakerTools;

    public const REFERENCE = 'delayed-wallet-1';

    public function __construct(
//        private readonly DelayedTransferFactory $delayedTransferFactory,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
//        /** @var User $user */
//        $user = $this->getReference(UserFixture::REFERENCE);

//        $delayedWallet = $this->delayedTransferFactory->create(
//            $baseEngine,
//            $quoteEngine,
//            $leadQuoteWalletAddress,
//            $leadBaseExchangeAmount
//        );
//
//        $manager->persist($profile);
//        $manager->flush();
//
//        $this->addReference(self::REFERENCE, $delayedWallet);
    }

//    public function getDependencies(): array
//    {
//        return [
//            UserFixture::class,
//        ];
//    }
}
