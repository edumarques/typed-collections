<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Exception\Exception;
use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;

class TypedCollection extends AbstractTypedCollection implements
    TypedCollectionInterface,
    TypedCollectionMutableInterface
{
    /**
     * @codeCoverageIgnore
     * @throws Exception
     */
    public static function createFromImmutable(TypedCollectionImmutable $collection): static
    {
        return new static($collection->getType(), $collection->getItems());
    }

    public function clear(): static
    {
        $this->items = [];

        return $this;
    }

    /**
     * @throws Exception
     */
    public function add(mixed $item): static
    {
        $this->validateValue($item);

        $this->items[] = $item;

        return $this;
    }

    public function filter(callable $condition): static
    {
        $items = [];

        foreach ($this->getItems() as $item) {
            if ($condition($item)) {
                $items[] = $item;
            }
        }

        $this->items = $items;

        return $this;
    }

    public function reverse(): static
    {
        $this->items = array_reverse($this->getItems());

        return $this;
    }

    public function sort(callable $callback): static
    {
        usort($this->items, $callback);

        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function map(callable $callable): static
    {
        $items = [];

        foreach ($this->getItems() as $item) {
            $result = $callable($item);
            $items[] = $result;
        }

        $this->type = false === empty($items) ? TypeMapper::getType($items[0]) : $this->type;
        $this->items = $items;

        return $this;
    }

    public function shuffle(): static
    {
        shuffle($this->items);

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function merge(TypedCollectionInterface $collection): static
    {
        /** @phpstan-ignore-next-line */
        $items = $collection->getItems();

        $this->validateValues($items);

        $this->items = array_merge($this->items, $items);

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function insertAt(int $index, mixed $item): static
    {
        $this->validateIndex($index);
        $this->validateValue($item);

        $items = $this->getItems();

        $partA = array_slice($items, 0, $index);
        $partB = array_slice($items, $index, count($items));
        $partA[] = $item;

        $this->items = array_merge($partA, $partB);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeAt(int $index): static
    {
        $this->validateIndex($index);
        $items = $this->getItems();

        $partA = array_slice($items, 0, $index);
        $partB = array_slice($items, $index + 1, count($items));
        $this->items = array_merge($partA, $partB);

        return $this;
    }

    public function dropFirst(): static
    {
        array_shift($this->items);

        return $this;
    }

    public function dropLast(): static
    {
        array_pop($this->items);

        return $this;
    }
}
