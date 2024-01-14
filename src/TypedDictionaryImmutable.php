<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Enum\ScalarType;
use EduardoMarques\TypedCollections\Exception\Exception;

class TypedDictionaryImmutable extends AbstractTypedDictionary implements
    TypedDictionaryInterface,
    TypedDictionaryImmutableInterface
{
    /**
     * @codeCoverageIgnore
     * @throws Exception
     */
    public static function createFromMutable(TypedDictionary $dictionary): static
    {
        return new static($dictionary->getKeyType(), $dictionary->getValueType(), $dictionary->getStorage());
    }

    /**
     * @throws Exception
     */
    public function clear(): static
    {
        return new static($this->getKeyType(), $this->getValueType());
    }

    /**
     * @throws Exception
     */
    public function set(int|string $key, mixed $value): static
    {
        $this->validateKey($key);
        $this->validateValue($value);

        $storage = $this->getStorage();
        $storage[$key] = $value;

        return new static($this->getKeyType(), $this->getValueType(), $storage);
    }

    /**
     * @throws Exception
     */
    public function remove(int|string $key): static
    {
        $this->hasKey($key);

        $storage = $this->getStorage();

        unset($storage[$key]);

        return new static($this->getKeyType(), $this->getValueType(), $storage);
    }

    /**
     * @throws Exception
     */
    public function filter(callable $condition): static
    {
        $storage = [];

        foreach ($this->getStorage() as $key => $value) {
            if ($condition($key, $value)) {
                $storage[$key] = $value;
            }
        }

        return new static($this->getKeyType(), $this->getValueType(), $storage);
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

        $keyType = $keyType ?? $this->getKeyType();
        $valueType = $valueType ?? $this->getValueType();

        return new static($keyType, $valueType, $storage);
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

        $storage = array_merge($this->getStorage(), $storage);

        return new static($this->getKeyType(), $this->getValueType(), $storage);
    }

    /**
     * @throws Exception
     */
    public function dropFirst(): static
    {
        $firstKey = $this->firstKey();

        $storage = $this->getStorage();

        unset($storage[$firstKey]);

        return new static($this->getKeyType(), $this->getValueType(), $storage);
    }

    /**
     * @throws Exception
     */
    public function dropLast(): static
    {
        $lastKey = $this->lastKey();

        $storage = $this->getStorage();

        unset($storage[$lastKey]);

        return new static($this->getKeyType(), $this->getValueType(), $storage);
    }

    /**
     * @throws Exception
     */
    public function toCollection(): TypedCollectionInterface
    {
        return TypedCollectionImmutable::create($this->getValueType(), $this->values());
    }
}
