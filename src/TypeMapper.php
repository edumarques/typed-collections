<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Enum\NonScalarType;
use EduardoMarques\TypedCollections\Enum\ScalarType;
use EduardoMarques\TypedCollections\Enum\TypeInterface;
use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;

class TypeMapper
{
    /**
     * @return TypeInterface|class-string
     * @throws InvalidArgumentException
     */
    public static function getType(mixed $value): TypeInterface|string
    {
        if ($value instanceof TypeInterface) {
            return $value;
        }

        if (is_callable($value)) {
            return NonScalarType::CALLABLE;
        }

        if (is_object($value)) {
            return get_class($value);
        }

        $primitiveType = gettype($value);

        try {
            return ScalarType::getFromPrimitiveType($primitiveType);
        } catch (InvalidArgumentException) {
        }

        try {
            return NonScalarType::getFromPrimitiveType($primitiveType);
        } catch (InvalidArgumentException) {
        }

        throw new InvalidArgumentException("The type '$primitiveType' is not supported");
    }
}
