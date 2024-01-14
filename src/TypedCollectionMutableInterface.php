<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

interface TypedCollectionMutableInterface
{
    public static function createFromImmutable(TypedCollectionImmutable $collection): static;
}
