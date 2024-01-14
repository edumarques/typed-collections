<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Enum\TypeInterface;
use EduardoMarques\TypedCollections\Exception\Exception;
use EduardoMarques\TypedCollections\Exception\OutOfRangeException;

abstract class AbstractTypedCollection implements TypedCollectionInterface
{
    use TypeValidatorTrait;

    /**
     * @param TypeInterface|class-string $type
     * @param array<int, mixed> $items
     *
     * @throws Exception
     */
    final protected function __construct(
        protected TypeInterface|string $type,
        protected array $items = []
    ) {
        $this->type = $this->determineValueType($type);

        if (empty($items)) {
            return;
        }

        $items = array_values($items);
        $this->validateValues($items);

        $this->items = $items;
    }

    /**
     * @inheritDoc
     */
    public static function create(TypeInterface|string $type, array $items = []): static
    {
        return new static($type, $items);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getType(): TypeInterface|string
    {
        return $this->type;
    }

    public function contains(mixed $item): bool
    {
        return in_array($item, $this->items, true);
    }

    /**
     * @inheritDoc
     */
    public function at(int $index): mixed
    {
        $this->validateIndex($index);

        return $this->items[$index];
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function slice(int $offset, ?int $length = null): static
    {
        $items = array_slice($this->items, $offset, $length);

        return new static($this->type, $items);
    }

    public function reduce(callable $callable, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callable, $initial);
    }

    public function firstIndex(): ?int
    {
        $items = $this->items;
        reset($items);

        return key($items);
    }

    public function lastIndex(): ?int
    {
        $items = $this->items;
        end($items);

        return key($items);
    }

    public function first(): mixed
    {
        $items = $this->items;

        return reset($items) ?: null;
    }

    public function last(): mixed
    {
        $items = $this->items;

        return end($items) ?: null;
    }

    /**
     * @inheritDoc
     *
     * @codeCoverageIgnore
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     *
     * @codeCoverageIgnore
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return array<int, mixed>
     */
    protected function getItems(): array
    {
        return $this->items;
    }

    /**
     * @throws OutOfRangeException
     */
    protected function validateIndex(int $index): void
    {
        if (!array_key_exists($index, $this->items)) {
            throw new OutOfRangeException('Index out of collection bounds');
        }
    }
}
