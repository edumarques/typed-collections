<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

class TypedCollection extends AbstractTypedCollection implements TypedCollectionInterface
{
    /**
     * @inheritDoc
     */
    public function clear(): TypedCollectionInterface
    {
        $this->items = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function add($item): TypedCollectionInterface
    {
        $this->validateItem($item);

        $this->items[] = $item;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filter(callable $condition): TypedCollectionInterface
    {
        $items = [];

        foreach ($this->items as $item) {
            if ($condition($item)) {
                $items[] = $item;
            }
        }

        $this->items = $items;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function reverse(bool $preserveKeys = false): TypedCollectionInterface
    {
        $this->items = array_reverse($this->items, $preserveKeys);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function sort(callable $callback): TypedCollectionInterface
    {
        usort($this->items, $callback);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $callable): TypedCollectionInterface
    {
        $items = [];
        $type = null;

        foreach ($this->items as $item) {
            $result = $callable($item);
            $items[] = $result;

            if ($type === null) {
                $type = gettype($result);
                $type = $type === 'object' ? get_class($result) : $type;
            }
        }

        $this->type = $type ?? $this->type;
        $this->items = $items;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function shuffle(): TypedCollectionInterface
    {
        shuffle($this->items);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function merge(TypedCollectionInterface $typedCollection): TypedCollectionInterface
    {
        $items = $typedCollection->toArray();

        $this->validateItems($items);

        $this->items = array_merge($this->items, $items);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function insertAt(int $index, $item): TypedCollectionInterface
    {
        $this->validateIndex($index);
        $this->validateItem($item);

        $partA = array_slice($this->items, 0, $index);
        $partB = array_slice($this->items, $index, count($this->items));
        $partA[] = $item;

        $this->items = array_merge($partA, $partB);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeAt(int $index): TypedCollectionInterface
    {
        $this->validateIndex($index);
        $items = $this->items;

        $partA = array_slice($items, 0, $index);
        $partB = array_slice($items, $index + 1, count($items));
        $this->items = array_merge($partA, $partB);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dropFirst(): TypedCollectionInterface
    {
        array_shift($this->items);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dropLast(): TypedCollectionInterface
    {
        array_pop($this->items);

        return $this;
    }
}
