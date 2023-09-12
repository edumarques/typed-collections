<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Enum\TypeEnum;
use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;

trait TypeValidatorTrait
{
    /**
     * @var string[]
     */
    protected $supportedScalarTypes = [TypeEnum::STRING, TypeEnum::INTEGER, TypeEnum::DOUBLE, TypeEnum::BOOLEAN];

    /**
     * @var string[]
     */
    protected $supportedNonScalarTypes = [TypeEnum::ARRAY, TypeEnum::CALLABLE];

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

        if (!in_array($scalarType, [TypeEnum::INTEGER, TypeEnum::STRING])) {
            throw new InvalidArgumentException('This type is not supported for keys');
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
            TypeEnum::INT => TypeEnum::INTEGER,
            TypeEnum::FLOAT => TypeEnum::DOUBLE,
            TypeEnum::BOOL => TypeEnum::BOOLEAN,
        ];

        $type = $synonyms[$type] ?? $type;

        return in_array($type, $this->supportedScalarTypes) ? $type : null;
    }

    /**
     * @param mixed[] $keyValueTuples
     *
     * @throws InvalidArgumentException
     */
    protected function validateKeys(array $keyValueTuples): void
    {
        foreach ($keyValueTuples as $key => $value) {
            $this->validateKey($key);
        }
    }

    /**
     * @param mixed[] $keyValueTuples
     *
     * @throws InvalidArgumentException
     */
    protected function validateValues(array $keyValueTuples): void
    {
        foreach ($keyValueTuples as $value) {
            $this->validateValue($value);
        }
    }

    /**
     * @param string|int $key
     *
     * @throws InvalidArgumentException
     */
    protected function validateKey($key): void
    {
        $keyType = null;

        if (property_exists($this, 'keyType')) {
            $keyType = $this->keyType;
        }

        if (gettype($key) !== $keyType) {
            throw new InvalidArgumentException("Key is not of type: $keyType");
        }
    }

    /**
     * @param mixed $value
     *
     * @throws InvalidArgumentException
     */
    protected function validateValue($value): void
    {
        $type = null;

        if (property_exists($this, 'valueType')) {
            $type = $this->valueType;
        }

        if (property_exists($this, 'type')) {
            $type = $this->type;
        }

        $requiredTypeIsCallable = $type === TypeEnum::CALLABLE;
        $itemType = gettype($value);
        $itemIsObject = $itemType === TypeEnum::OBJECT;

        if ($requiredTypeIsCallable && !is_callable($value)) {
            throw new InvalidArgumentException('Value must be callable');
        }

        if (!$requiredTypeIsCallable && $itemIsObject && !($value instanceof $type)) {
            throw new InvalidArgumentException("Value is not type or subtype of $type");
        }

        if (!$requiredTypeIsCallable && !$itemIsObject && $itemType !== $type) {
            throw new InvalidArgumentException("Value is not of type: $type");
        }
    }
}
