<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;

abstract class AbstractTypedDictionary implements TypedDictionaryInterface
{
    use TypeValidatorTrait;

    /**
     * @var array<string|int, mixed>
     */
    protected $storage = [];

    /**
     * @var string
     */
    protected $keyType;

    /**
     * @var string
     */
    protected $valueType;

    /**
     * @param array<string|int, mixed>|null $keyValueTuples
     *
     * @throws InvalidArgumentException
     */
    final protected function __construct(string $keyType, string $valueType, ?array $keyValueTuples = null)
    {
        $this->keyType = $this->determineKeyType($keyType);
        $this->valueType = $this->determineValueType($valueType);

        if ($keyValueTuples === null) {
            return;
        }

        $this->validateKeys($keyValueTuples);
        $this->validateValues($keyValueTuples);

        $this->storage = $keyValueTuples;
    }

    /**
     * @inheritDoc
     */
    public static function create(
        string $keyType,
        string $valueType,
        ?array $keyValueTuples = null
    ): TypedDictionaryInterface {
        return new static($keyType, $valueType, $keyValueTuples);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getKeyType(): string
    {
        return $this->keyType;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getValueType(): string
    {
        return $this->valueType;
    }

    /**
     * @inheritDoc
     */
    public function hasKey($key): bool
    {
        $this->validateKey($key);

        return array_key_exists($key, $this->storage);
    }

    /**
     * @inheritDoc
     */
    public function hasValue($value): bool
    {
        $this->validateValue($value);

        return in_array($value, $this->storage, true);
    }

    /**
     * @inheritDoc
     */
    public function get($key)
    {
        return $this->hasKey($key) ? $this->storage[$key] : null;
    }

    /**
     * @inheritDoc
     */
    public function keys(): array
    {
        return array_keys($this->storage);
    }

    /**
     * @inheritDoc
     */
    public function values(): array
    {
        return array_values($this->storage);
    }

    /**
     * @inheritDoc
     */
    public function reduce(callable $callable, $initial = null)
    {
        return array_reduce($this->storage, $callable, $initial);
    }

    /**
     * @inheritDoc
     */
    public function firstKey()
    {
        $storage = $this->storage;
        reset($storage);

        return key($storage);
    }

    /**
     * @inheritDoc
     */
    public function lastKey()
    {
        $storage = $this->storage;
        end($storage);

        return key($storage);
    }

    /**
     * @inheritDoc
     */
    public function firstValue()
    {
        $firstKey = $this->firstKey();

        return $this->storage[$firstKey] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function lastValue()
    {
        $lastKey = $this->lastKey();

        return $this->storage[$lastKey] ?? null;
    }

    /**
     * @inheritDoc
     *
     * @codeCoverageIgnore
     */
    public function toArray(): array
    {
        return $this->storage;
    }

    /**
     * @inheritDoc
     *
     * @codeCoverageIgnore
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->storage);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->storage);
    }
}
