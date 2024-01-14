<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Exception\Exception;

class TypedCollectionImmutable extends AbstractTypedCollection implements
    TypedCollectionInterface,
    TypedCollectionImmutableInterface
{
    /**
     * @codeCoverageIgnore
     * @throws Exception
     */
    public static function createFromMutable(TypedCollection $collection): static
    {
        return new static($collection->getType(), $collection->getItems());
    }

    /**
     * @throws Exception
     */
    public function clear(): static
    {
        return new static($this->getType());
    }

    /**
     * @throws Exception
     */
    public function add(mixed $item): static
    {
        $this->validateValue($item);

        $items = $this->getItems();
        $items[] = $item;

        return new static($this->getType(), $items);
    }

    /**
     * @throws Exception
     */
    public function filter(callable $condition): static
    {
        $items = [];

        foreach ($this->getItems() as $item) {
            if ($condition($item)) {
                $items[] = $item;
            }
        }

        return new static($this->getType(), $items);
    }

    /**
     * @throws Exception
     */
    public function reverse(): static
    {
        $items = array_reverse($this->getItems());

        return new static($this->getType(), $items);
    }

    /**
     * @throws Exception
     */
    public function sort(callable $callback): static
    {
        $items = $this->getItems();

        usort($items, $callback);

        return new static($this->getType(), $items);
    }

    /**
     * @throws Exception
     */
    public function map(callable $callable): static
    {
        $items = [];

        foreach ($this->getItems() as $item) {
            $result = $callable($item);
            $items[] = $result;
        }

        $type = false === empty($items) ? TypeMapper::getType($items[0]) : $this->getType();

        return new static($type, $items);
    }

    /**
     * @throws Exception
     */
    public function shuffle(): static
    {
        $items = $this->getItems();
        shuffle($items);

        return new static($this->getType(), $items);
    }

    /**
     * @throws Exception
     */
    public function merge(TypedCollectionInterface $collection): static
    {
        /** @phpstan-ignore-next-line */
        $items = $collection->getItems();

        $this->validateValues($items);

        $newItems = array_merge($this->getItems(), $items);

        return new static($this->getType(), $newItems);
    }

    /**
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

        $items = array_merge($partA, $partB);

        return new static($this->getType(), $items);
    }

    /**
     * @throws Exception
     */
    public function removeAt(int $index): static
    {
        $this->validateIndex($index);
        $items = $this->getItems();

        $partA = array_slice($items, 0, $index);
        $partB = array_slice($items, $index + 1, count($items));
        $items = array_merge($partA, $partB);

        return new static($this->getType(), $items);
    }

    /**
     * @throws Exception
     */
    public function dropFirst(): static
    {
        $items = $this->getItems();
        array_shift($items);

        return new static($this->getType(), $items);
    }

    /**
     * @throws Exception
     */
    public function dropLast(): static
    {
        $items = $this->getItems();
        array_pop($items);

        return new static($this->getType(), $items);
    }
}
