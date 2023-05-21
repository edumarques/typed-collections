<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

interface TypedCollectionImmutableInterface
{
    /**
     * @return static
     */
    public static function createFromMutable(TypedCollection $collection): self;
}
