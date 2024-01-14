<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

interface TypedDictionaryMutableInterface
{
    public static function createFromImmutable(TypedDictionaryImmutable $dictionary): static;
}
