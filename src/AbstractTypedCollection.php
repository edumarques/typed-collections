<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;
use EduardoMarques\TypedCollections\Exception\OutOfRangeException;

abstract class AbstractTypedCollection implements TypedCollectionInterface
{
    use TypeValidatorTrait;

    /**
     * @var array<int, mixed>
     */
    protected $items = [];

    /**
     * @var string
     */
    protected $type;

    /**
     * @param array<int, mixed>|null $items
     *
     * @throws InvalidArgumentException
     */
    final protected function __construct(string $type, ?array $items = null)
    {
        $this->type = $this->determineValueType($type);

        if ($items === null) {
            return;
        }

        $items = array_values($items);
        $this->validateValues($items);

        $this->items = $items;
    }

    /**
     * @inheritDoc
     */
    public static function create(string $type, ?array $items = null): TypedCollectionInterface
    {
        return new static($type, $items);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function contains($item): bool
    {
        return in_array($item, $this->items, true);
    }

    /**
     * @inheritDoc
     */
    public function at(int $index)
    {
        $this->validateIndex($index);

        return $this->items[$index];
    }

    /**
     * @inheritDoc
     */
    public function slice(int $offset, ?int $length = null): TypedCollectionInterface
    {
        $items = array_slice($this->items, $offset, $length);

        return new static($this->type, $items);
    }

    /**
     * @inheritDoc
     */
    public function reduce(callable $callable, $initial = null)
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

    /**
     * @inheritDoc
     */
    public function first()
    {
        $items = $this->items;

        return reset($items) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function last()
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
     * @throws OutOfRangeException
     */
    protected function validateIndex(int $index): void
    {
        if (!array_key_exists($index, $this->items)) {
            throw new OutOfRangeException('Index out of collection bounds');
        }
    }
}
