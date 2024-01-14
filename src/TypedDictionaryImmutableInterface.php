<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

interface TypedDictionaryImmutableInterface
{
    public static function createFromMutable(TypedDictionary $dictionary): static;
}
