<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

interface TypedDictionaryImmutableInterface
{
    /**
     * @return static
     */
    public static function createFromMutable(TypedDictionary $dictionary): self;
}
