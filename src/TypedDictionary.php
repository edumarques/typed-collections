<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Enum\ScalarType;
use EduardoMarques\TypedCollections\Exception\Exception;

class TypedDictionary extends AbstractTypedDictionary implements
    TypedDictionaryInterface,
    TypedDictionaryMutableInterface
{
    /**
     * @codeCoverageIgnore
     * @throws Exception
     */
    public static function createFromImmutable(TypedDictionaryImmutable $dictionary): static
    {
        return new static($dictionary->getKeyType(), $dictionary->getValueType(), $dictionary->getStorage());
    }

    public function clear(): static
    {
        $this->storage = [];

        return $this;
    }

    /**
     * @throws Exception
     */
    public function set(int|string $key, mixed $value): static
    {
        $this->validateKey($key);
        $this->validateValue($value);

        $this->storage[$key] = $value;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function remove(int|string $key): static
    {
        if ($this->hasKey($key)) {
            unset($this->storage[$key]);
        }

        return $this;
    }

    public function filter(callable $condition): static
    {
        $storage = [];

        foreach ($this->getStorage() as $key => $value) {
            if ($condition($key, $value)) {
                $storage[$key] = $value;
            }
        }

        $this->storage = $storage;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function map(callable $callable): static
    {
        $storage = [];

        foreach ($this->getStorage() as $key => $value) {
            $result = $callable($key, $value);
            [$newKey, $newValue] = is_array($result) ? $result : [$key, $result];
            $storage[$newKey] = $newValue;
        }

        if (false === empty($storage)) {
            $first = reset($storage);
            $keyPrimitiveType = gettype(key($storage));
            $keyType = ScalarType::getFromPrimitiveType($keyPrimitiveType);
            $valueType = TypeMapper::getType($first);
        }

        $this->keyType = $keyType ?? $this->getKeyType();
        $this->valueType = $valueType ?? $this->getValueType();
        $this->storage = $storage;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function merge(TypedDictionaryInterface $dictionary): static
    {
        /** @phpstan-ignore-next-line */
        $storage = $dictionary->getStorage();

        $this->validateKeys($storage);
        $this->validateValues($storage);

        $this->storage = array_merge($this->getStorage(), $storage);

        return $this;
    }

    public function dropFirst(): static
    {
        $firstKey = $this->firstKey();

        unset($this->storage[$firstKey]);

        return $this;
    }

    public function dropLast(): static
    {
        $lastKey = $this->lastKey();

        unset($this->storage[$lastKey]);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function toCollection(): TypedCollectionInterface
    {
        return TypedCollection::create($this->getValueType(), $this->values());
    }
}
