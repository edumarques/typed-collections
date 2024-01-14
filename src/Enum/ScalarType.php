<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections\Enum;

use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;

enum ScalarType implements TypeInterface
{
    case INTEGER;
    case STRING;
    case BOOLEAN;
    case DOUBLE;

    #[\Override]
    public static function getFromPrimitiveType(string $type): self
    {
        return match ($type) {
            'string' => self::STRING,
            'int', 'integer' => self::INTEGER,
            'bool', 'boolean' => self::BOOLEAN,
            'float', 'double' => self::DOUBLE,
            default => throw new InvalidArgumentException('This type is not scalar or is not supported')
        };
    }

    public static function isValidKeyType(self $type): bool
    {
        return in_array($type, [self::INTEGER, self::STRING]);
    }
}
