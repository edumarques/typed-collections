<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

interface TypedCollectionMutableInterface
{
    /**
     * @return static
     */
    public static function createFromImmutable(TypedCollectionImmutable $collection): self;
}
