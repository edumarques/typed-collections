<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Enum\TypeInterface;
use EduardoMarques\TypedCollections\Exception\Exception;
use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;
use EduardoMarques\TypedCollections\Exception\OutOfRangeException;

/**
 * @template-extends \IteratorAggregate<int, mixed>
 */
interface TypedCollectionInterface extends \IteratorAggregate, \Countable
{
    /**
     * @param TypeInterface|class-string|string $type
     * @param array<int, mixed> $items
     *
     * @throws Exception
     */
    public static function create(TypeInterface|string $type, array $items = []): static;

    public function getType(): TypeInterface|string;

    public function clear(): static;

    public function contains(mixed $item): bool;

    public function add(mixed $item): static;

    /**
     * @throws OutOfRangeException
     */
    public function at(int $index): mixed;

    public function filter(callable $condition): static;

    public function reverse(): static;

    public function sort(callable $callback): static;

    public function map(callable $callable): static;

    public function shuffle(): static;

    /**
     * @throws InvalidArgumentException
     */
    public function merge(self $collection): static;

    /**
     * @throws InvalidArgumentException
     */
    public function slice(int $offset, ?int $length = null): static;

    public function reduce(callable $callable, mixed $initial = null): mixed;

    /**
     * @throws InvalidArgumentException
     * @throws OutOfRangeException
     */
    public function insertAt(int $index, mixed $item): static;

    /**
     * @throws OutOfRangeException
     */
    public function removeAt(int $index): static;

    public function dropFirst(): static;

    public function dropLast(): static;

    public function firstIndex(): ?int;

    public function lastIndex(): ?int;

    public function first(): mixed;

    public function last(): mixed;

    /**
     * @return mixed[]
     */
    public function toArray(): array;

    /**
     * @return \ArrayIterator<int, mixed>
     */
    public function getIterator(): \ArrayIterator;

    /**
     * @inheritDoc
     */
    public function count(): int;
}
