<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;
use EduardoMarques\TypedCollections\Exception\OutOfRangeException;

/**
 * @template-extends \IteratorAggregate<int, self>
 */
interface TypedCollectionInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param mixed[]|null $items
     *
     * @throws InvalidArgumentException
     */
    public static function create(string $type, ?array $items = null): self;

    public function getType(): string;

    /**
     * @return static
     */
    public function clear(): self;

    /**
     * @param mixed $item
     */
    public function contains($item): bool;

    /**
     * @param mixed $item
     *
     * @return static
     */
    public function add($item): self;

    /**
     * @return mixed
     * @throws OutOfRangeException
     */
    public function at(int $index);

    /**
     * @return static
     */
    public function filter(callable $condition): self;

    /**
     * @return static
     */
    public function reverse(): self;

    /**
     * @return static
     */
    public function sort(callable $callback): self;

    /**
     * @return static
     */
    public function map(callable $callable): self;

    /**
     * @return static
     */
    public function shuffle(): self;

    /**
     * @return static
     */
    public function merge(self $typedCollection): self;

    /**
     * @return static
     * @throws InvalidArgumentException
     */
    public function slice(int $offset, ?int $length = null): self;

    /**
     * @param mixed $initial
     *
     * @return mixed
     */
    public function reduce(callable $callable, $initial = null);

    /**
     * @param mixed $item
     *
     * @return static
     * @throws InvalidArgumentException
     * @throws OutOfRangeException
     */
    public function insertAt(int $index, $item): self;

    /**
     * @return static
     * @throws OutOfRangeException
     */
    public function removeAt(int $index): self;

    /**
     * @return static
     */
    public function dropFirst(): self;

    /**
     * @return static
     */
    public function dropLast(): self;

    public function firstIndex(): ?int;

    public function lastIndex(): ?int;

    /**
     * @return mixed
     */
    public function first();

    /**
     * @return mixed
     */
    public function last();

    /**
     * @return mixed[]
     */
    public function toArray(): array;

    /**
     * @return \ArrayIterator<int, self>
     */
    public function getIterator(): \ArrayIterator;

    /**
     * @inheritDoc
     */
    public function count(): int;
}
