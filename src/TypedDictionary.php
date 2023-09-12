<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Enum\TypeEnum;

class TypedDictionary extends AbstractTypedDictionary implements
    TypedDictionaryInterface,
    TypedDictionaryMutableInterface
{
    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public static function createFromImmutable(TypedDictionaryImmutable $dictionary): TypedDictionaryMutableInterface
    {
        return new static($dictionary->getKeyType(), $dictionary->getValueType(), $dictionary->toArray());
    }

    /**
     * @inheritDoc
     */
    public function clear(): TypedDictionaryInterface
    {
        $this->storage = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value): TypedDictionaryInterface
    {
        $this->validateKey($key);
        $this->validateValue($value);

        $this->storage[$key] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove($key): TypedDictionaryInterface
    {
        if ($this->hasKey($key)) {
            unset($this->storage[$key]);
        }

        return $this;
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

        $this->storage = $storage;

        return $this;
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

        $this->keyType = $keyType ?? $this->keyType;
        $this->valueType = $valueType ?? $this->valueType;
        $this->storage = $storage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function merge(TypedDictionaryInterface $dictionary): TypedDictionaryInterface
    {
        $keyValueTuples = $dictionary->toArray();

        $this->validateKeys($keyValueTuples);
        $this->validateValues($keyValueTuples);

        $this->storage = array_merge($this->storage, $keyValueTuples);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dropFirst(): TypedDictionaryInterface
    {
        $firstKey = $this->firstKey();

        unset($this->storage[$firstKey]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dropLast(): TypedDictionaryInterface
    {
        $lastKey = $this->lastKey();

        unset($this->storage[$lastKey]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function unique(bool $preserveKeys = false): TypedDictionaryInterface
    {
        $keyType = $preserveKeys ? $this->keyType : TypeEnum::INT;

        $unique = static::create($keyType, $this->valueType);

        $defaultKey = 0;

        foreach ($this->storage as $key => $value) {
            if ($unique->hasValue($value)) {
                continue;
            }

            $uniqueKey = $preserveKeys ? $key : $defaultKey++;

            $unique->set($uniqueKey, $value);
        }

        $this->storage = $unique->storage;

        return $this;
    }

    public function toCollection(): TypedCollectionInterface
    {
        return TypedCollection::create($this->valueType, $this->values());
    }
}
