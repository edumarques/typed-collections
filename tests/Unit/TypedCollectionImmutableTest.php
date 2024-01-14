<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections\Tests\Unit;

use EduardoMarques\TypedCollections\Enum\NonScalarType;
use EduardoMarques\TypedCollections\Enum\ScalarType;
use EduardoMarques\TypedCollections\Exception\Exception;
use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;
use EduardoMarques\TypedCollections\Exception\OutOfRangeException;
use EduardoMarques\TypedCollections\TypedCollectionImmutable;
use PHPUnit\Framework\TestCase;

final class TypedCollectionImmutableTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCreateWithInvalidCallableItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: CALLABLE');

        TypedCollectionImmutable::create(NonScalarType::CALLABLE, ['test']);
    }

    /**
     * @throws Exception
     */
    public function testCreateWithInvalidObjectItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: Traversable');

        TypedCollectionImmutable::create(\Traversable::class, [new \stdClass()]);
    }

    /**
     * @throws Exception
     */
    public function testCreateWithInvalidNonObjectItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: stdClass');

        TypedCollectionImmutable::create(\stdClass::class, ['test']);
    }

    /**
     * @throws Exception
     */
    public function testCreateWithInvalidNonTypeItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This type is not supported or does not exist');

        TypedCollectionImmutable::create('object');
    }

    /**
     * @throws Exception
     */
    public function testContainsWhenItDoesNot(): void
    {
        $item1 = new \stdClass();
        $collection = TypedCollectionImmutable::create(\stdClass::class, [$item1]);

        $item2 = new \stdClass();

        self::assertFalse($collection->contains($item2));
    }

    /**
     * @throws Exception
     */
    public function testContainsWhenItDoes(): void
    {
        $item1 = new \stdClass();
        $collection = TypedCollectionImmutable::create(\stdClass::class, [$item1]);

        self::assertTrue($collection->contains($item1));
    }

    /**
     * @throws Exception
     */
    public function testAtWithInvalidIndex(): void
    {
        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER);

        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Index out of collection bounds');

        $collection->at(0);
    }

    /**
     * @throws Exception
     * @throws OutOfRangeException
     */
    public function testAtWithValidIndex(): void
    {
        $item1 = 'test';
        $collection = TypedCollectionImmutable::create(ScalarType::STRING, [$item1]);

        self::assertSame($item1, $collection->at(0));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function testSlice(): void
    {
        $item1 = 'test1';
        $item2 = 'test2';
        $item3 = 'test3';
        $item4 = 'test4';

        $collection = TypedCollectionImmutable::create(ScalarType::STRING, [$item1, $item2, $item3, $item4]);

        $expected = TypedCollectionImmutable::create(ScalarType::STRING, [$item2, $item3]);

        self::assertEquals($expected, $collection->slice(1, 2));
    }

    /**
     * @throws Exception
     */
    public function testReduce(): void
    {
        $item1 = 1.5;
        $item2 = 1.0;
        $item3 = 3.0;
        $item4 = 45.5;

        $collection = TypedCollectionImmutable::create(ScalarType::DOUBLE, [$item1, $item2, $item3, $item4]);

        $actual = $collection->reduce(
            static function (float $carry, float $item): float {
                return $carry + $item;
            },
            10
        );

        self::assertSame(61.0, $actual);
    }

    /**
     * @throws Exception
     */
    public function testFirstIndex(): void
    {
        $item1 = 1.5;
        $item2 = 1.0;
        $item3 = 3.0;
        $item4 = 45.5;

        $collection = TypedCollectionImmutable::create(ScalarType::DOUBLE, [$item1, $item2, $item3, $item4]);

        self::assertSame(0, $collection->firstIndex());
    }

    /**
     * @throws Exception
     */
    public function testFirstIndexWhenCollectionIsEmpty(): void
    {
        $collection = TypedCollectionImmutable::create(ScalarType::DOUBLE);

        self::assertNull($collection->firstIndex());
    }

    /**
     * @throws Exception
     */
    public function testLastIndex(): void
    {
        $item1 = 1.5;
        $item2 = 1.0;
        $item3 = 3.0;
        $item4 = 45.5;

        $collection = TypedCollectionImmutable::create(ScalarType::DOUBLE, [$item1, $item2, $item3, $item4]);

        self::assertSame(3, $collection->lastIndex());
    }

    /**
     * @throws Exception
     */
    public function testLastIndexWhenCollectionIsEmpty(): void
    {
        $collection = TypedCollectionImmutable::create(ScalarType::DOUBLE);

        self::assertNull($collection->lastIndex());
    }

    /**
     * @throws Exception
     */
    public function testFirst(): void
    {
        $item1 = static function (): bool {
            return true;
        };

        $item2 = static function (): bool {
            return false;
        };

        $collection = TypedCollectionImmutable::create(NonScalarType::CALLABLE, [$item1, $item2]);

        self::assertSame($item1, $collection->first());
    }

    /**
     * @throws Exception
     */
    public function testLast(): void
    {
        $item1 = static function (): bool {
            return true;
        };

        $item2 = static function (): bool {
            return false;
        };

        $collection = TypedCollectionImmutable::create(NonScalarType::CALLABLE, [$item1, $item2]);

        self::assertSame($item2, $collection->last());
    }

    /**
     * @throws Exception
     */
    public function testCount(): void
    {
        $item1 = 1;
        $item2 = 2;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2]);

        self::assertSame(2, $collection->count());
    }

    /**
     * @throws Exception
     */
    public function testClear(): void
    {
        $item1 = 1;
        $item2 = 2;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2]);

        $newCollection = $collection->clear();

        self::assertSame([$item1, $item2], $collection->toArray());
        self::assertSame([], $newCollection->toArray());
    }

    /**
     * @throws Exception
     */
    public function testAddWithInvalidItem(): void
    {
        $item1 = 1;
        $item2 = 2.0;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: INTEGER');

        $collection->add($item2);
    }

    /**
     * @throws Exception
     */
    public function testAddWithValidItem(): void
    {
        $item1 = 1;
        $item2 = 2;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1]);

        $newCollection = $collection->add($item2);

        self::assertSame([$item1], $collection->toArray());
        self::assertSame([$item1, $item2], $newCollection->toArray());
    }

    /**
     * @throws Exception
     */
    public function testFilter(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        $newCollection = $collection->filter(
            static function (int $item): bool {
                return $item % 2 === 0;
            }
        );

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item2], $newCollection->toArray());
    }

    /**
     * @throws Exception
     */
    public function testReverse(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        $newCollection = $collection->reverse();

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item3, $item2, $item1], $newCollection->toArray());
    }

    /**
     * @throws Exception
     */
    public function testSort(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item3, $item2]);

        $newCollection = $collection->sort(
            static function (int $itemA, int $itemB): int {
                return $itemB <=> $itemA;
            }
        );

        self::assertSame([$item1, $item3, $item2], $collection->toArray());
        self::assertSame([$item3, $item2, $item1], $newCollection->toArray());
    }

    /**
     * @throws Exception
     */
    public function testMap(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        $newCollection = $collection->map(
            static function (int $item): string {
                return "item$item";
            }
        );

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame(['item1', 'item2', 'item3'], $newCollection->toArray());
    }

    /**
     * @throws Exception
     */
    public function testMapReturningObjectCollection(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        $newCollection = $collection->map(
            static function (int $item): \stdClass {
                $object = new \stdClass();
                $object->id = $item;
                return $object;
            }
        );

        $mappedItem1 = new \stdClass();
        $mappedItem1->id = 1;
        $mappedItem2 = new \stdClass();
        $mappedItem2->id = 2;
        $mappedItem3 = new \stdClass();
        $mappedItem3->id = 3;

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertEquals([$mappedItem1, $mappedItem2, $mappedItem3], $newCollection->toArray());
    }

    /**
     * @throws Exception
     */
    public function testShuffle(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        $newCollection = $collection->shuffle();

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame(3, $newCollection->count());
        self::assertTrue($newCollection->contains($item1));
        self::assertTrue($newCollection->contains($item2));
        self::assertTrue($newCollection->contains($item3));
    }

    /**
     * @throws Exception
     */
    public function testMerge(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection1 = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1]);
        $collection2 = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item2, $item3]);

        $newCollection = $collection1->merge($collection2);

        self::assertSame([$item1], $collection1->toArray());
        self::assertSame([$item2, $item3], $collection2->toArray());
        self::assertSame([$item1, $item2, $item3], $newCollection->toArray());
    }

    /**
     * @throws Exception
     */
    public function testMergeWithInvalidValues(): void
    {
        $item1 = 1;
        $item2 = '2';
        $item3 = '3';

        $collection1 = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1]);
        $collection2 = TypedCollectionImmutable::create(ScalarType::STRING, [$item2, $item3]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: INTEGER');

        $collection1->merge($collection2);
    }

    /**
     * @throws Exception
     */
    public function testInsertAtWithInvalidIndex(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;
        $item4 = 4;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Index out of collection bounds');

        $collection->insertAt(3, $item4);
    }

    /**
     * @throws Exception
     */
    public function testInsertAtWithInvalidItem(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: INTEGER');

        $collection->insertAt(2, 1.0);
    }

    /**
     * @throws Exception
     */
    public function testInsertAt(): void
    {
        $item0 = 0;
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        $newCollection = $collection->insertAt(0, $item0);

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item0, $item1, $item2, $item3], $newCollection->toArray());
    }

    /**
     * @throws Exception
     */
    public function testRemoveAtWithInvalidIndex(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Index out of collection bounds');

        $collection->removeAt(3);
    }

    /**
     * @throws Exception
     */
    public function testRemoveAt(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        $newCollection = $collection->removeAt(1);

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item1, $item3], $newCollection->toArray());
    }

    /**
     * @throws Exception
     */
    public function testDropFirst(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        $newCollection = $collection->dropFirst();

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item2, $item3], $newCollection->toArray());
    }

    /**
     * @throws Exception
     */
    public function testDropLast(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create(ScalarType::INTEGER, [$item1, $item2, $item3]);

        $newCollection = $collection->dropLast();

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item1, $item2], $newCollection->toArray());
    }
}
