<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

class TypedDictionaryImmutable extends AbstractTypedDictionary implements
    TypedDictionaryInterface,
    TypedDictionaryImmutableInterface
{
    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public static function createFromMutable(TypedDictionary $dictionary): TypedDictionaryImmutableInterface
    {
        return new static($dictionary->getKeyType(), $dictionary->getValueType(), $dictionary->toArray());
    }

    /**
     * @inheritDoc
     */
    public function clear(): TypedDictionaryInterface
    {
        return new static($this->keyType, $this->valueType);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value): TypedDictionaryInterface
    {
        $this->validateKey($key);
        $this->validateValue($value);

        $storage = $this->storage;
        $storage[$key] = $value;

        return new static($this->keyType, $this->valueType, $storage);
    }

    /**
     * @inheritDoc
     */
    public function remove($key): TypedDictionaryInterface
    {
        $this->hasKey($key);

        $storage = $this->storage;

        unset($storage[$key]);

        return new static($this->keyType, $this->valueType, $storage);
    }

    /**
     * @inheritDoc
     */
    public function filter(callable $condition): TypedDictionaryInterface
    {
        $storage = [];

        foreach ($this->storage as $key => $value) {
            if ($condition($key, $value)) {
                $storage[$key] = $value;
            }
        }

        return new static($this->keyType, $this->valueType, $storage);
    }

    /**
     * @inheritDoc
     */
    public function map(callable $callable): TypedDictionaryInterface
    {
        $storage = [];
        $keyType = null;
        $valueType = null;

        foreach ($this->storage as $key => $value) {
            $result = $callable($key, $value);
            [$newKey, $newValue] = is_array($result) ? $result : [$key, $result];

            if ($keyType === null && $valueType === null) {
                $keyType = gettype($newKey);
                $valueType = gettype($newValue);
                $valueType = $valueType === 'object' ? get_class($newValue) : $valueType;
            }

            $storage[$newKey] = $newValue;
        }

        $keyType = $keyType ?? $this->keyType;
        $valueType = $valueType ?? $this->valueType;

        return new static($keyType, $valueType, $storage);
    }

    /**
     * @inheritDoc
     */
    public function merge(TypedDictionaryInterface $dictionary): TypedDictionaryInterface
    {
        $keyValueTuples = $dictionary->toArray();

        $this->validateKeys($keyValueTuples);
        $this->validateValues($keyValueTuples);

        $storage = array_merge($this->storage, $keyValueTuples);

        return new static($this->keyType, $this->valueType, $storage);
    }

    /**
     * @inheritDoc
     */
    public function dropFirst(): TypedDictionaryInterface
    {
        $firstKey = $this->firstKey();

        $storage = $this->storage;

        unset($storage[$firstKey]);

        return new static($this->keyType, $this->valueType, $storage);
    }

    /**
     * @inheritDoc
     */
    public function dropLast(): TypedDictionaryInterface
    {
        $lastKey = $this->lastKey();

        $storage = $this->storage;

        unset($storage[$lastKey]);

        return new static($this->keyType, $this->valueType, $storage);
    }

    public function toCollection(): TypedCollectionInterface
    {
        return TypedCollectionImmutable::create($this->valueType, $this->values());
    }
}
