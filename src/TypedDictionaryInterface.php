<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;

/**
 * @template-extends \IteratorAggregate<string|int, mixed>
 */
interface TypedDictionaryInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param mixed[]|null $keyValueTuples
     *
     * @return static
     * @throws InvalidArgumentException
     */
    public static function create(string $keyType, string $valueType, ?array $keyValueTuples = null): self;

    public function getKeyType(): string;

    public function getValueType(): string;

    /**
     * @return static
     */
    public function clear(): self;

    /**
     * @param int|string $key
     *
     * @throws InvalidArgumentException
     */
    public function hasKey($key): bool;

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    public function hasValue($value): bool;

    /**
     * @param int|string $key
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get($key);

    /**
     * @param int|string $key
     * @param mixed $value
     *
     * @return static
     * @throws InvalidArgumentException
     */
    public function set($key, $value): self;

    /**
     * @param int|string $key
     *
     * @return static
     * @throws InvalidArgumentException
     */
    public function remove($key): self;

    /**
     * @return string[]|int[]
     */
    public function keys(): array;

    /**
     * @return mixed[]
     */
    public function values(): array;

    /**
     * @return static
     */
    public function filter(callable $condition): self;

    /**
     * @return static
     */
    public function map(callable $callable): self;

    /**
     * @return static
     * @throws InvalidArgumentException
     */
    public function merge(self $dictionary): self;

    /**
     * @param mixed $initial
     *
     * @return mixed
     */
    public function reduce(callable $callable, $initial = null);

    /**
     * @return static
     */
    public function dropFirst(): self;

    /**
     * @return static
     */
    public function dropLast(): self;

    /**
     * @return string|int|null
     */
    public function firstKey();

    /**
     * @return string|int|null
     */
    public function lastKey();

    /**
     * @return mixed
     */
    public function firstValue();

    /**
     * @return mixed
     */
    public function lastValue();

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
