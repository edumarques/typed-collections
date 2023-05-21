<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections\Tests\Unit;

use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;
use EduardoMarques\TypedCollections\TypedCollectionImmutable;
use EduardoMarques\TypedCollections\TypedDictionaryImmutable;
use PHPUnit\Framework\TestCase;

final class TypedDictionaryImmutableTest extends TestCase
{
    public function testCreateWithInvalidCallableValue(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value must be callable');

        TypedDictionaryImmutable::create('int', 'callable', ['test']);
    }

    public function testCreateWithInvalidObjectValue(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not type or subtype of Traversable');

        TypedDictionaryImmutable::create('int', \Traversable::class, [new \stdClass()]);
    }

    public function testCreateWithInvalidNonObjectValue(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: stdClass');

        TypedDictionaryImmutable::create('int', \stdClass::class, ['test']);
    }

    public function testCreateWithInvalidNonTypeValue(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This type is not supported or does not exist');

        TypedDictionaryImmutable::create('int', 'object');
    }

    public function testCreateWithInvalidNonTypeKey(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This type is not supported for keys');

        TypedDictionaryImmutable::create('float', 'int');
    }

    public function testHasKeyWithInvalidKey(): void
    {
        $dictionary = TypedDictionaryImmutable::create('string', 'string');

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Key is not of type: string');

        $dictionary->hasKey(0);
    }

    public function testHasKeyWhenItDoesNot(): void
    {
        $dictionary = TypedDictionaryImmutable::create('string', 'string');

        self::assertFalse($dictionary->hasKey('testKey'));
    }

    public function testHasKeyWhenItDoes(): void
    {
        $dictionary = TypedDictionaryImmutable::create('string', 'string', ['testKey' => 'testValue']);

        self::assertTrue($dictionary->hasKey('testKey'));
    }

    public function testHasValueWithInvalidValue(): void
    {
        $dictionary = TypedDictionaryImmutable::create('string', 'string');

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: string');

        $dictionary->hasValue(0);
    }

    public function testHasValueWhenItDoesNot(): void
    {
        $dictionary = TypedDictionaryImmutable::create('string', 'string');

        self::assertFalse($dictionary->hasValue('testValue'));
    }

    public function testHasValueWhenItDoes(): void
    {
        $dictionary = TypedDictionaryImmutable::create('string', 'string', ['testKey' => 'testValue']);

        self::assertTrue($dictionary->hasValue('testValue'));
    }

    public function testGetWithInvalidKey(): void
    {
        $dictionary = TypedDictionaryImmutable::create('string', 'string');

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Key is not of type: string');

        $dictionary->get(0);
    }

    public function testGetWithNonExistentKey(): void
    {
        $dictionary = TypedDictionaryImmutable::create('string', 'string');

        self::assertNull($dictionary->get('testKey'));
    }

    public function testGet(): void
    {
        $dictionary = TypedDictionaryImmutable::create('string', 'string', ['testKey' => 'testValue']);

        self::assertSame('testValue', $dictionary->get('testKey'));
    }

    public function testKeys(): void
    {
        $dictionary = TypedDictionaryImmutable::create(
            'string',
            'string',
            ['testKey1' => 'testValue1', 'testKey2' => 'testValue2']
        );

        self::assertSame(['testKey1', 'testKey2'], $dictionary->keys());
    }

    public function testValues(): void
    {
        $dictionary = TypedDictionaryImmutable::create(
            'string',
            'string',
            ['testKey1' => 'testValue1', 'testKey2' => 'testValue2']
        );

        self::assertSame(['testValue1', 'testValue2'], $dictionary->values());
    }

    public function testReduce(): void
    {
        $value1 = 1.5;
        $value2 = 1.0;
        $value3 = 3.0;
        $value4 = 45.5;

        $dictionary = TypedDictionaryImmutable::create('int', 'float', [$value1, $value2, $value3, $value4]);

        $actual = $dictionary->reduce(
            static function (float $carry, float $value): float {
                return $carry + $value;
            },
            10
        );

        self::assertSame(61.0, $actual);
    }

    public function testFirstKey(): void
    {
        $value1 = 1.5;
        $value2 = 1.0;
        $value3 = 3.0;
        $value4 = 45.5;

        $dictionary = TypedDictionaryImmutable::create('int', 'float', [$value1, $value2, $value3, $value4]);

        self::assertSame(0, $dictionary->firstKey());
    }

    public function testFirstKeyWhenCollectionIsEmpty(): void
    {
        $dictionary = TypedDictionaryImmutable::create('int', 'float');

        self::assertNull($dictionary->firstKey());
    }

    public function testLastKey(): void
    {
        $value1 = 1.5;
        $value2 = 1.0;
        $value3 = 3.0;
        $value4 = 45.5;

        $dictionary = TypedDictionaryImmutable::create('int', 'float', [$value1, $value2, $value3, $value4]);

        self::assertSame(3, $dictionary->lastKey());
    }

    public function testLastKeyWhenCollectionIsEmpty(): void
    {
        $dictionary = TypedDictionaryImmutable::create('int', 'float');

        self::assertNull($dictionary->lastKey());
    }

    public function testFirstValue(): void
    {
        $value1 = static function (): bool {
            return true;
        };

        $value2 = static function (): bool {
            return false;
        };

        $dictionary = TypedDictionaryImmutable::create('int', 'callable', [$value1, $value2]);

        self::assertSame($value1, $dictionary->firstValue());
    }

    public function testLastValue(): void
    {
        $value1 = static function (): bool {
            return true;
        };

        $value2 = static function (): bool {
            return false;
        };

        $dictionary = TypedDictionaryImmutable::create('int', 'callable', [$value1, $value2]);

        self::assertSame($value2, $dictionary->lastValue());
    }

    public function testCount(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2]);

        self::assertSame(2, $dictionary->count());
    }

    public function testClear(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2]);

        $newDictionary = $dictionary->clear();

        self::assertSame([$value1, $value2], $dictionary->toArray());
        self::assertSame([], $newDictionary->toArray());
    }

    public function testSetWithInvalidKey(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Key is not of type: integer');

        $dictionary->set('testKey', 3);
    }

    public function testSetWithInvalidValue(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: integer');

        $dictionary->set(2, 'testValue');
    }

    public function testSet(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2]);

        $newDictionary = $dictionary->set(2, $value3);

        self::assertSame([$value1, $value2], $dictionary->toArray());
        self::assertSame([$value1, $value2, $value3], $newDictionary->toArray());
    }

    public function testRemoveWithInvalidKey(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Key is not of type: integer');

        $dictionary->remove('testKey');
    }

    public function testRemoveWithNonExistentKey(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2]);

        $newDictionary = $dictionary->remove(2);

        self::assertSame([$value1, $value2], $dictionary->toArray());
        self::assertSame([$value1, $value2], $newDictionary->toArray());
    }

    public function testRemove(): void
    {
        $value1 = 1;
        $value2 = 2;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2]);

        $newDictionary = $dictionary->remove(0);

        self::assertSame([$value1, $value2], $dictionary->toArray());
        self::assertSame([1 => $value2], $newDictionary->toArray());
    }

    public function testFilter(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2, $value3]);

        $newDictionary = $dictionary->filter(
            static function (int $key, int $value): bool {
                return $value % 2 === 0;
            }
        );

        self::assertSame([$value1, $value2, $value3], $dictionary->toArray());
        self::assertSame([1 => $value2], $newDictionary->toArray());
    }

    public function testMap(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2, $value3]);

        $newDictionary = $dictionary->map(
            static function (int $key, int $value): string {
                return "value$value";
            }
        );

        self::assertSame([$value1, $value2, $value3], $dictionary->toArray());
        self::assertSame(['value1', 'value2', 'value3'], $newDictionary->toArray());
    }

    public function testMapReturningObjectDictionary(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2, $value3]);

        $newDictionary = $dictionary->map(
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

        self::assertEquals([$value1, $value2, $value3], $dictionary->toArray());
        self::assertEquals([$mappedItem1, $mappedItem2, $mappedItem3], $newDictionary->toArray());
    }

    public function testMapKeyAndValue(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2, $value3]);

        $newDictionary = $dictionary->map(
            static function (int $key, int $value): array {
                return ["key$key", "value$value"];
            }
        );

        self::assertSame([$value1, $value2, $value3], $dictionary->toArray());
        self::assertSame(['key0' => 'value1', 'key1' => 'value2', 'key2' => 'value3'], $newDictionary->toArray());
    }

    public function testMerge(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary1 = TypedDictionaryImmutable::create('int', 'int', [$value1]);
        $dictionary2 = TypedDictionaryImmutable::create('int', 'int', [$value2, $value3]);

        $newDictionary = $dictionary1->merge($dictionary2);

        self::assertEquals([$value1], $dictionary1->toArray());
        self::assertEquals([$value2, $value3], $dictionary2->toArray());
        self::assertEquals([$value1, $value2, $value3], $newDictionary->toArray());
    }

    public function testMergeWithInvalidKeys(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary1 = TypedDictionaryImmutable::create('int', 'int', [$value1]);
        $dictionary2 = TypedDictionaryImmutable::create('string', 'int', ['key1' => $value2, 'key2' => $value3]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Key is not of type: integer');

        $dictionary1->merge($dictionary2);
    }

    public function testMergeWithInvalidValues(): void
    {
        $value1 = 1;
        $value2 = '2';
        $value3 = '3';

        $dictionary1 = TypedDictionaryImmutable::create('int', 'int', [$value1]);
        $dictionary2 = TypedDictionaryImmutable::create('int', 'string', [$value2, $value3]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: integer');

        $dictionary1->merge($dictionary2);
    }

    public function testDropFirst(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2, $value3]);

        $newDictionary = $dictionary->dropFirst();

        self::assertSame([$value1, $value2, $value3], $dictionary->toArray());
        self::assertSame([1 => $value2, 2 => $value3], $newDictionary->toArray());
    }

    public function testDropLast(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionaryImmutable::create('int', 'int', [$value1, $value2, $value3]);

        $newDictionary = $dictionary->dropLast();

        self::assertSame([$value1, $value2, $value3], $dictionary->toArray());
        self::assertSame([0 => $value1, 1 => $value2], $newDictionary->toArray());
    }

    public function testToCollection(): void
    {
        $value1 = 1;
        $value2 = 2;
        $value3 = 3;

        $dictionary = TypedDictionaryImmutable::create(
            'string',
            'int',
            ['key1' => $value1, 'key2' => $value2, 'key3' => $value3]
        );

        $expected = TypedCollectionImmutable::create('int', [$value1, $value2, $value3]);

        self::assertEquals($expected, $dictionary->toCollection());
    }
}
