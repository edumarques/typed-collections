<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections\Enum;

use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;

interface TypeInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public static function getFromPrimitiveType(string $type): self;
}
