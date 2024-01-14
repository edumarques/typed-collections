<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Enum\ScalarType;
use EduardoMarques\TypedCollections\Enum\TypeInterface;
use EduardoMarques\TypedCollections\Exception\Exception;
use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;

/**
 * @template-extends \IteratorAggregate<string|int, mixed>
 */
interface TypedDictionaryInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param TypeInterface|class-string|string $valueType
     * @param array<int|string, mixed> $storage
     *
     * @throws Exception
     */
    public static function create(
        ScalarType $keyType,
        TypeInterface|string $valueType,
        array $storage = []
    ): static;

    public function getKeyType(): ScalarType;

    public function getValueType(): TypeInterface|string;

    public function clear(): static;

    /**
     * @throws InvalidArgumentException
     */
    public function hasKey(int|string $key): bool;

    /**
     * @throws InvalidArgumentException
     */
    public function hasValue(mixed $value): bool;

    /**
     * @throws InvalidArgumentException
     */
    public function get(int|string $key): mixed;

    /**
     * @throws InvalidArgumentException
     */
    public function set(int|string $key, mixed $value): static;

    /**
     * @throws InvalidArgumentException
     */
    public function remove(int|string $key): static;

    /**
     * @return string[]|int[]
     */
    public function keys(): array;

    /**
     * @return mixed[]
     */
    public function values(): array;

    public function filter(callable $condition): static;

    public function map(callable $callable): static;

    /**
     * @throws InvalidArgumentException
     */
    public function merge(self $dictionary): static;

    public function reduce(callable $callable, mixed $initial = null): mixed;

    public function dropFirst(): static;

    public function dropLast(): static;

    public function firstKey(): int|string|null;

    public function lastKey(): int|string|null;

    public function firstValue(): mixed;

    public function lastValue(): mixed;

    public function toCollection(): TypedCollectionInterface;

    /**
     * @return mixed[]
     */
    public function toArray(): array;

    /**
     * @return \ArrayIterator<string|int, mixed>
     */
    public function getIterator(): \ArrayIterator;

    /**
     * @inheritDoc
     */
    public function count(): int;
}
