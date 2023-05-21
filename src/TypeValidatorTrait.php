<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;
use EduardoMarques\TypedCollections\Exception\OutOfRangeException;

trait TypeValidatorTrait
{
    /**
     * @var string[]
     */
    protected $supportedScalarTypes = ['string', 'integer', 'double', 'boolean'];

    /**
     * @var string[]
     */
    protected $supportedNonScalarTypes = ['array', 'callable'];

    /**
     * @throws InvalidArgumentException
     */
    protected function determineValueType(string $type): string
    {
        if ($this->nonScalarTypeExists($type)) {
            return $type;
        }

        $scalarType = $this->getScalarType($type);

        if ($scalarType === null) {
            throw new InvalidArgumentException('This type is not supported or does not exist');
        }

        return $scalarType;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function determineKeyType(string $type): string
    {
        $scalarType = $this->getScalarType($type);

        if ($scalarType === null) {
            throw new InvalidArgumentException('This type is not supported or does not exist');
        }

        if (!in_array($scalarType, ['integer', 'string'])) {
            throw new InvalidArgumentException('This type is not supported as a key');
        }

        return $scalarType;
    }

    protected function nonScalarTypeExists(string $type): bool
    {
        return class_exists($type)
            || interface_exists($type)
            || in_array($type, $this->supportedNonScalarTypes);
    }

    protected function getScalarType(string $type): ?string
    {
        $synonyms = [
            'int' => 'integer',
            'float' => 'double',
            'bool' => 'boolean',
        ];

        $type = $synonyms[$type] ?? $type;

        return in_array($type, $this->supportedScalarTypes) ? $type : null;
    }

    /**
     * @param mixed[] $items
     *
     * @throws InvalidArgumentException
     */
    protected function validateItems(array $items): void
    {
        foreach ($items as $item) {
            $this->validateItem($item);
        }
    }

    /**
     * @param mixed $item
     *
     * @throws InvalidArgumentException
     */
    protected function validateItem($item): void
    {
        $itemType = gettype($item);
        $requiredTypeIsCallable = $this->type === 'callable';
        $itemIsObject = $itemType === 'object';

        if ($requiredTypeIsCallable && !is_callable($item)) {
            throw new InvalidArgumentException('Item must be callable');
        }

        if (!$requiredTypeIsCallable && $itemIsObject && !($item instanceof $this->type)) {
            throw new InvalidArgumentException("Item is not type or subtype of $this->type");
        }

        if (!$requiredTypeIsCallable && !$itemIsObject && $itemType !== $this->type) {
            throw new InvalidArgumentException("Item is not of type: $this->type");
        }
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
