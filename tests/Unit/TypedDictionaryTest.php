<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections\Tests\Unit;

use EduardoMarques\TypedCollections\Enum\NonScalarType;
use EduardoMarques\TypedCollections\Enum\ScalarType;
use EduardoMarques\TypedCollections\Exception\Exception;
use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;
use EduardoMarques\TypedCollections\TypedCollection;
use EduardoMarques\TypedCollections\TypedDictionary;
use PHPUnit\Framework\TestCase;

final class TypedDictionaryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateWithInvalidCallableValue(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: CALLABLE');

        TypedDictionary::create(ScalarType::INTEGER, NonScalarType::CALLABLE, ['test']);
    }

    /**
     * @throws Exception
     */
    public function testCreateWithInvalidObjectValue(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: Traversable');

        TypedDictionary::create(ScalarType::INTEGER, \Traversable::class, [new \stdClass()]);
    }

    /**
     * @throws Exception
     */
    public function testCreateWithInvalidNonObjectValue(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: stdClass');

        TypedDictionary::create(ScalarType::INTEGER, \stdClass::class, ['test']);
    }

    /**
     * @throws Exception
     */
    public function testCreateWithInvalidNonTypeValue(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This type is not supported or does not exist');

        TypedDictionary::create(ScalarType::INTEGER, 'object');
    }

    /**
     * @throws Exception
     */
    public function testCreateWithInvalidNonTypeKey(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This type is not supported for keys');

        TypedDictionary::create(ScalarType::DOUBLE, ScalarType::INTEGER);
    }

    /**
     * @throws Exception
     */
    public function testHasKeyWithInvalidKey(): void
    {
        $dictionary = TypedDictionary::create(ScalarType::STRING, ScalarType::STRING);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Key is not of type: STRING');

        $dictionary->hasKey(0);
    }

    /**
     * @throws Exception
     */
    public function testHasKeyWhenItDoesNot(): void
    {
        $dictionary = TypedDictionary::create(ScalarType::STRING, ScalarType::STRING);

        self::assertFalse($dictionary->hasKey('testKey'));
    }

    /**
     * @throws Exception
     */
    public function testHasKeyWhenItDoes(): void
    {
        $dictionary = TypedDictionary::create(ScalarType::STRING, ScalarType::STRING, ['testKey' => 'testValue']);

        self::assertTrue($dictionary->hasKey('testKey'));
    }

    /**
     * @throws Exception
     */
    public function testHasValueWithInvalidValue(): void
    {
        $dictionary = TypedDictionary::create(ScalarType::STRING, ScalarType::STRING);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: STRING');

        $dictionary->hasValue(0);
    }

    /**
     * @throws Exception
     */
    public function testHasValueWhenItDoesNot(): void
    {
        $dictionary = TypedDictionary::create(ScalarType::STRING, ScalarType::STRING);

        self::assertFalse($dictionary->hasValue('testValue'));
    }

    /**
     * @throws Exception
     */
    public function testHasValueWhenItDoes(): void
    {
        $dictionary = TypedDictionary::create(ScalarType::STRING, ScalarType::STRING, ['testKey' => 'testValue']);

        self::assertTrue($dictionary->hasValue('testValue'));
    }

    /**
     * @throws Exception
     */
    public function testGetWithInvalidKey(): void
    {
        $dictionary = TypedDictionary::create(ScalarType::STRING, ScalarType::STRING);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Key is not of type: STRING');

        $dictionary->get(0);
    }

    /**
     * @throws Exception
     */
    public function testGetWithNonExistentKey(): void
    {
        $dictionary = TypedDictionary::create(ScalarType::STRING, ScalarType::STRING);

        self::assertNull($dictionary->get('testKey'));
    }

    /**
     * @throws Exception
     */
    public function testGet(): void
    {
        $dictionary = TypedDictionary::create(ScalarType::STRING, ScalarType::STRING, ['testKey' => 'testValue']);

        self::assertSame('testValue', $dictionary->get('testKey'));
    }

    /**
     * @throws Exception
     */
    public function testKeys(): void
    {
        $dictionary = TypedDictionary::create(
            ScalarType::STRING,
            ScalarType::STRING,
            ['testKey1' => 'testValue1', 'testKey2' => 'testValue2']
        );

        self::assertSame(['testKey1', 'testKey2'], $dictionary->keys());
    }

    /**
     * @throws Exception
     */
    public function testValues(): void
    {
        $dictionary = TypedDictionary::create(
            ScalarType::STRING,
            ScalarType::STRING,
            ['testKey1' => 'testValue1', 'testKey2' => 'testValue2']
        );

        self::assertSame(['testValue1', 'testValue2'], $dictionary->values());
    }

    /**
     * @throws Exception
     */
    public function testReduce(): void
    {
        $value1 = 1.5;
        $value2 = 1.0;
        $value3 = 3.0;
        $value4 = 45.5;

        $dictionary = TypedDictionary::create(
            ScalarType::INTEGER,
            ScalarType::DOUBLE,
            [$value1, $value2, $value3, $value4]
        );

        $actual = $dictionary->reduce(
            static function (float $carry, float $value): float {
                return $carry + $value;
            },
            10
        );

        self::assertSame(61.0, $actual);
    }

    /**
     * @throws Exception
     */
    public function testFirstKey(): void
    {
        $value1 = 1.5;
        $value2 = 1.0;
        $value3 = 3.0;
        $value4 = 45.5;

        $dictionary = TypedDictionary::create(
            ScalarType::INTEGER,
            ScalarType::DOUBLE,
            [$value1, $value2, $value3, $value4]
        );

        self::assertSame(0, $dictionary->firstKey());
    }

    /**
     * @throws Exception
     */
    public function testFirstKeyWhenCollectionIsEmpty(): void
    {
        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::DOUBLE);

        self::assertNull($dictionary->firstKey());
    }

    /**
     * @throws Exception
     */
    public function testLastKey(): void
    {
        $value1 = 1.5;
        $value2 = 1.0;
        $value3 = 3.0;
        $value4 = 45.5;

        $dictionary = TypedDictionary::create(
            ScalarType::INTEGER,
            ScalarType::DOUBLE,
            [$value1, $value2, $value3, $value4]
        );

        self::assertSame(3, $dictionary->lastKey());
    }

    /**
     * @throws Exception
     */
    public function testLastKeyWhenCollectionIsEmpty(): void
    {
        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::DOUBLE);

        self::assertNull($dictionary->lastKey());
    }

    /**
     * @throws Exception
     */
    public function testFirstValue(): void
    {
        $value1 = static function (): bool {
            return true;
        };

        $value2 = static function (): bool {
            return false;
        };

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, NonScalarType::CALLABLE, [$value1, $value2]);

        self::assertSame($value1, $dictionary->firstValue());
    }

    /**
     * @throws Exception
     */
    public function testLastValue(): void
    {
        $value1 = static function (): bool {
            return true;
        };

        $value2 = static function (): bool {
            return false;
        };

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, NonScalarType::CALLABLE, [$value1, $value2]);

        self::assertSame($value2, $dictionary->lastValue());
    }

    /**
     * @throws Exception
     */
    public function testCount(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2]);

        self::assertSame(2, $dictionary->count());
    }

    /**
     * @throws Exception
     */
    public function testClear(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2]);

        $dictionary->clear();

        self::assertSame([], $dictionary->toArray());
    }

    /**
     * @throws Exception
     */
    public function testSetWithInvalidKey(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Key is not of type: INTEGER');

        $dictionary->set('testKey', 3);
    }

    /**
     * @throws Exception
     */
    public function testSetWithInvalidValue(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: INTEGER');

        $dictionary->set(2, 'testValue');
    }

    /**
     * @throws Exception
     */
    public function testSet(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2]);

        $dictionary->set(2, $value3);

        self::assertSame([$value1, $value2, $value3], $dictionary->toArray());
    }

    /**
     * @throws Exception
     */
    public function testRemoveWithInvalidKey(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Key is not of type: INTEGER');

        $dictionary->remove('testKey');
    }

    /**
     * @throws Exception
     */
    public function testRemoveWithNonExistentKey(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2]);

        $dictionary->remove(2);

        self::assertSame([$value1, $value2], $dictionary->toArray());
    }

    /**
     * @throws Exception
     */
    public function testRemove(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2]);

        $dictionary->remove(0);

        self::assertSame([1 => $value2], $dictionary->toArray());
    }

    /**
     * @throws Exception
     */
    public function testFilter(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2, $value3]);

        $dictionary->filter(
            static function (int $key, int $value): bool {
                return $value % 2 === 0;
            }
        );

        self::assertSame([1 => $value2], $dictionary->toArray());
    }

    /**
     * @throws Exception
     */
    public function testMap(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2, $value3]);

        $dictionary->map(
            static function (int $key, int $value): string {
                return "value$value";
            }
        );

        self::assertSame(['value1', 'value2', 'value3'], $dictionary->toArray());
    }

    /**
     * @throws Exception
     */
    public function testMapReturningObjectDictionary(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2, $value3]);

        $dictionary->map(
            static function (int $key, int $value): \stdClass {
                $object = new \stdClass();
                $object->id = $value;
                return $object;
            }
        );

        $mappedItem1 = new \stdClass();
        $mappedItem1->id = 1;
        $mappedItem2 = new \stdClass();
        $mappedItem2->id = 2;
        $mappedItem3 = new \stdClass();
        $mappedItem3->id = 3;

        self::assertEquals([$mappedItem1, $mappedItem2, $mappedItem3], $dictionary->toArray());
    }

    /**
     * @throws Exception
     */
    public function testMapKeyAndValue(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2, $value3]);

        $dictionary->map(
            static function (int $key, int $value): array {
                return ["key$key", "value$value"];
            }
        );

        self::assertSame(['key0' => 'value1', 'key1' => 'value2', 'key2' => 'value3'], $dictionary->toArray());
    }

    /**
     * @throws Exception
     */
    public function testMerge(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary1 = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1]);
        $dictionary2 = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value2, $value3]);

        $dictionary1->merge($dictionary2);

        self::assertEquals([$value1, $value2, $value3], $dictionary1->toArray());
    }

    /**
     * @throws Exception
     */
    public function testMergeWithInvalidKeys(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary1 = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1]);
        $dictionary2 = TypedDictionary::create(
            ScalarType::STRING,
            ScalarType::INTEGER,
            ['key1' => $value2, 'key2' => $value3]
        );

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Key is not of type: INTEGER');

        $dictionary1->merge($dictionary2);
    }

    /**
     * @throws Exception
     */
    public function testMergeWithInvalidValues(): void
    {
        $value1 = 1;
        $value2 = '2';
        $value3 = '3';

        $dictionary1 = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1]);
        $dictionary2 = TypedDictionary::create(ScalarType::INTEGER, ScalarType::STRING, [$value2, $value3]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: INTEGER');

        $dictionary1->merge($dictionary2);
    }

    /**
     * @throws Exception
     */
    public function testDropFirst(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2, $value3]);

        $dictionary->dropFirst();

        self::assertSame([1 => $value2, 2 => $value3], $dictionary->toArray());
    }

    /**
     * @throws Exception
     */
    public function testDropLast(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionary::create(ScalarType::INTEGER, ScalarType::INTEGER, [$value1, $value2, $value3]);

        $dictionary->dropLast();

        self::assertSame([0 => $value1, 1 => $value2], $dictionary->toArray());
    }

    /**
     * @throws Exception
     */
    public function testToCollection(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionary::create(
            ScalarType::STRING,
            ScalarType::INTEGER,
            ['key1' => $value1, 'key2' => $value2, 'key3' => $value3]
        );

        $expected = TypedCollection::create(ScalarType::INTEGER, [$value1, $value2, $value3]);

        self::assertEquals($expected, $dictionary->toCollection());
    }
}
