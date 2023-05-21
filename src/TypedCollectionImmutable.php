<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

class TypedCollectionImmutable extends AbstractTypedCollection implements
    TypedCollectionInterface,
    TypedCollectionImmutableInterface
{
    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public static function createFromMutable(TypedCollection $collection): TypedCollectionImmutableInterface
    {
        return new static($collection->getType(), $collection->toArray());
    }

    /**
     * @inheritDoc
     */
    public function clear(): TypedCollectionInterface
    {
        return new static($this->type);
    }

    /**
     * @inheritDoc
     */
    public function add($item): TypedCollectionInterface
    {
        $this->validateValue($item);

        $items = $this->items;
        $items[] = $item;

        return new static($this->type, $items);
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

        return new static($this->type, $items);
    }

    /**
     * @inheritdoc
     */
    public function reverse(): TypedCollectionInterface
    {
        $items = array_reverse($this->items);

        return new static($this->type, $items);
    }

    /**
     * @inheritdoc
     */
    public function sort(callable $callback): TypedCollectionInterface
    {
        $items = $this->items;

        usort($items, $callback);

        return new static($this->type, $items);
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

        return new static($type ?? $this->type, $items);
    }

    /**
     * @inheritDoc
     */
    public function shuffle(): TypedCollectionInterface
    {
        $items = $this->items;
        shuffle($items);

        return new static($this->type, $items);
    }

    /**
     * @inheritDoc
     */
    public function merge(TypedCollectionInterface $collection): TypedCollectionInterface
    {
        $items = $collection->toArray();

        $this->validateValues($items);

        $newItems = array_merge($this->items, $items);

        return new static($this->type, $newItems);
    }

    /**
     * @inheritDoc
     */
    public function insertAt(int $index, $item): TypedCollectionInterface
    {
        $this->validateIndex($index);
        $this->validateValue($item);

        $partA = array_slice($this->items, 0, $index);
        $partB = array_slice($this->items, $index, count($this->items));
        $partA[] = $item;

        $items = array_merge($partA, $partB);

        return new static($this->type, $items);
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
        $items = array_merge($partA, $partB);

        return new static($this->type, $items);
    }

    /**
     * @inheritDoc
     */
    public function dropFirst(): TypedCollectionInterface
    {
        $items = $this->items;
        array_shift($items);

        return new static($this->type, $items);
    }

    /**
     * @inheritDoc
     */
    public function dropLast(): TypedCollectionInterface
    {
        $items = $this->items;
        array_pop($items);

        return new static($this->type, $items);
    }
}
