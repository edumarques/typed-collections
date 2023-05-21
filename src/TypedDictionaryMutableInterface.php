<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

interface TypedDictionaryMutableInterface
{
    /**
     * @return static
     */
    public static function createFromImmutable(TypedDictionaryImmutable $dictionary): self;
}
