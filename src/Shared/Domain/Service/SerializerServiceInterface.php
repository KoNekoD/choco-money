<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service;

interface SerializerServiceInterface
{
    public function serialize(mixed $data, string $format = 'json'): string;

    /**
     * @template T
     * @param class-string<T> $type
     * @return T
     */
    public function deserialize(mixed $data, string $type, string $format = 'json'): mixed;

    public function normalize(mixed $data, ?string $format = null): mixed;

    /**
     * @template T
     * @param class-string<T> $type
     * @return T
     */
    public function denormalize(mixed $data, string $type, ?string $format = null): mixed;
}
