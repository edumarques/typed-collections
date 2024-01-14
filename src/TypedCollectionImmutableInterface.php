<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

interface TypedCollectionImmutableInterface
{
    public static function createFromMutable(TypedCollection $collection): static;
}
