<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections\Enum;

use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;

enum NonScalarType implements TypeInterface
{
    case CALLABLE;
    case ARRAY;
    case OBJECT;

    #[\Override]
    public static function getFromPrimitiveType(string $type): self
    {
        return match ($type) {
            'callable' => self::CALLABLE,
            'array' => self::ARRAY,
            default => throw new InvalidArgumentException('This type is not non-scalar or is not supported')
        };
    }
}
