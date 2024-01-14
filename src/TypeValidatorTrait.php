<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Enum\NonScalarType;
use EduardoMarques\TypedCollections\Enum\ScalarType;
use EduardoMarques\TypedCollections\Enum\TypeInterface;
use EduardoMarques\TypedCollections\Exception\Exception;
use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;

trait TypeValidatorTrait
{
    /**
     * @throws InvalidArgumentException
     */
    protected function determineValueType(TypeInterface|string $type): TypeInterface|string
    {
        if (
            $type instanceof TypeInterface
            || class_exists($type)
            || interface_exists($type)
        ) {
            return $type;
        }

        throw new InvalidArgumentException('This type is not supported or does not exist');
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function determineKeyType(ScalarType $type): ScalarType
    {
        if (ScalarType::isValidKeyType($type)) {
            return $type;
        }

        throw new InvalidArgumentException('This type is not supported for keys');
    }

    /**
     * @param array<int|string, mixed> $keyValueTuples
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    protected function validateKeys(array $keyValueTuples): void
    {
        foreach ($keyValueTuples as $key => $value) {
            $this->validateKey($key);
        }
    }

    /**
     * @param array<int, mixed> $keyValueTuples
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    protected function validateValues(array $keyValueTuples): void
    {
        foreach ($keyValueTuples as $value) {
            $this->validateValue($value);
        }
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    protected function validateKey(int|string $key): void
    {
        /** @phpstan-ignore-next-line */
        if ($this->keyType !== TypeMapper::getType($key)) {
            /** @phpstan-ignore-next-line */
            throw new InvalidArgumentException("Key is not of type: {$this->keyType->name}");
        }
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    protected function validateValue(mixed $value): void
    {
        /** @var ScalarType|NonScalarType|string $type */
        /** @phpstan-ignore-next-line */
        $type = $this->valueType ?? $this->type;

        if ($type !== TypeMapper::getType($value)) {
            $typeString = is_object($type) ? $type->name : $type;

            throw new InvalidArgumentException("Value is not of type: $typeString");
        }
    }
}
