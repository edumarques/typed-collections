<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections;

use EduardoMarques\TypedCollections\Enum\ScalarType;
use EduardoMarques\TypedCollections\Enum\TypeInterface;
use EduardoMarques\TypedCollections\Exception\Exception;

abstract class AbstractTypedDictionary implements TypedDictionaryInterface
{
    use TypeValidatorTrait;

    /**
     * @param TypeInterface|class-string $valueType
     * @param array<int|string, mixed> $storage
     *
     * @throws Exception
     */
    final protected function __construct(
        protected ScalarType $keyType,
        protected TypeInterface|string $valueType,
        protected array $storage = []
    ) {
        $this->keyType = $this->determineKeyType($keyType);
        $this->valueType = $this->determineValueType($valueType);

        if (empty($storage)) {
            return;
        }

        $this->validateKeys($storage);
        $this->validateValues($storage);
    }

    /**
     * @inheritDoc
     */
    public static function create(
        ScalarType $keyType,
        TypeInterface|string $valueType,
        array $storage = []
    ): static {
        return new static($keyType, $valueType, $storage);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getKeyType(): ScalarType
    {
        return $this->keyType;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getValueType(): TypeInterface|string
    {
        return $this->valueType;
    }

    /**
     * @throws Exception
     */
    public function hasKey(int|string $key): bool
    {
        $this->validateKey($key);

        return array_key_exists($key, $this->storage);
    }

    /**
     * @throws Exception
     */
    public function hasValue(mixed $value): bool
    {
        $this->validateValue($value);

        return in_array($value, $this->storage, true);
    }

    /**
     * @throws Exception
     */
    public function get(int|string $key): mixed
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

    public function reduce(callable $callable, mixed $initial = null): mixed
    {
        return array_reduce($this->storage, $callable, $initial);
    }

    public function firstKey(): int|string|null
    {
        $storage = $this->storage;
        reset($storage);

        return key($storage);
    }

    public function lastKey(): int|string|null
    {
        $storage = $this->storage;
        end($storage);

        return key($storage);
    }

    public function firstValue(): mixed
    {
        $firstKey = $this->firstKey();

        return $this->storage[$firstKey] ?? null;
    }

    public function lastValue(): mixed
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

    /**
     * @return array<int|string, mixed>
     */
    protected function getStorage(): array
    {
        return $this->storage;
    }
}
