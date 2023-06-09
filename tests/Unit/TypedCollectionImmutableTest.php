<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections\Tests\Unit;

use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;
use EduardoMarques\TypedCollections\Exception\OutOfRangeException;
use EduardoMarques\TypedCollections\TypedCollectionImmutable;
use PHPUnit\Framework\TestCase;

final class TypedCollectionImmutableTest extends TestCase
{
    public function testCreateWithInvalidCallableItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value must be callable');

        TypedCollectionImmutable::create('callable', ['test']);
    }

    public function testCreateWithInvalidObjectItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not type or subtype of Traversable');

        TypedCollectionImmutable::create(\Traversable::class, [new \stdClass()]);
    }

    public function testCreateWithInvalidNonObjectItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: stdClass');

        TypedCollectionImmutable::create(\stdClass::class, ['test']);
    }

    public function testCreateWithInvalidNonTypeItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This type is not supported or does not exist');

        TypedCollectionImmutable::create('object');
    }

    public function testContainsWhenItDoesNot(): void
    {
        $item1 = new \stdClass();
        $collection = TypedCollectionImmutable::create(\stdClass::class, [$item1]);

        $item2 = new \stdClass();

        self::assertFalse($collection->contains($item2));
    }

    public function testContainsWhenItDoes(): void
    {
        $item1 = new \stdClass();
        $collection = TypedCollectionImmutable::create(\stdClass::class, [$item1]);

        self::assertTrue($collection->contains($item1));
    }

    public function testAtWithInvalidIndex(): void
    {
        $collection = TypedCollectionImmutable::create('integer');

        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Index out of collection bounds');

        $collection->at(0);
    }

    public function testAtWithValidIndex(): void
    {
        $item1 = 'test';
        $collection = TypedCollectionImmutable::create('string', [$item1]);

        self::assertSame($item1, $collection->at(0));
    }

    public function testSlice(): void
    {
        $item1 = 'test1';
        $item2 = 'test2';
        $item3 = 'test3';
        $item4 = 'test4';

        $collection = TypedCollectionImmutable::create('string', [$item1, $item2, $item3, $item4]);

        $expected = TypedCollectionImmutable::create('string', [$item2, $item3]);

        self::assertEquals($expected, $collection->slice(1, 2));
    }

    public function testReduce(): void
    {
        $item1 = 1.5;
        $item2 = 1.0;
        $item3 = 3.0;
        $item4 = 45.5;

        $collection = TypedCollectionImmutable::create('float', [$item1, $item2, $item3, $item4]);

        $actual = $collection->reduce(
            static function (float $carry, float $item): float {
                return $carry + $item;
            },
            10
        );

        self::assertSame(61.0, $actual);
    }

    public function testFirstIndex(): void
    {
        $item1 = 1.5;
        $item2 = 1.0;
        $item3 = 3.0;
        $item4 = 45.5;

        $collection = TypedCollectionImmutable::create('float', [$item1, $item2, $item3, $item4]);

        self::assertSame(0, $collection->firstIndex());
    }

    public function testFirstIndexWhenCollectionIsEmpty(): void
    {
        $collection = TypedCollectionImmutable::create('float');

        self::assertNull($collection->firstIndex());
    }

    public function testLastIndex(): void
    {
        $item1 = 1.5;
        $item2 = 1.0;
        $item3 = 3.0;
        $item4 = 45.5;

        $collection = TypedCollectionImmutable::create('float', [$item1, $item2, $item3, $item4]);

        self::assertSame(3, $collection->lastIndex());
    }

    public function testLastIndexWhenCollectionIsEmpty(): void
    {
        $collection = TypedCollectionImmutable::create('float');

        self::assertNull($collection->lastIndex());
    }

    public function testFirst(): void
    {
        $item1 = static function (): bool {
            return true;
        };

        $item2 = static function (): bool {
            return false;
        };

        $collection = TypedCollectionImmutable::create('callable', [$item1, $item2]);

        self::assertSame($item1, $collection->first());
    }

    public function testLast(): void
    {
        $item1 = static function (): bool {
            return true;
        };

        $item2 = static function (): bool {
            return false;
        };

        $collection = TypedCollectionImmutable::create('callable', [$item1, $item2]);

        self::assertSame($item2, $collection->last());
    }

    public function testCount(): void
    {
        $item1 = 1;
        $item2 = 2;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2]);

        self::assertSame(2, $collection->count());
    }

    public function testClear(): void
    {
        $item1 = 1;
        $item2 = 2;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2]);

        $newCollection = $collection->clear();

        self::assertSame([$item1, $item2], $collection->toArray());
        self::assertSame([], $newCollection->toArray());
    }

    public function testAddWithInvalidItem(): void
    {
        $item1 = 1;
        $item2 = 2.0;

        $collection = TypedCollectionImmutable::create('int', [$item1]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: integer');

        $collection->add($item2);
    }

    public function testAddWithValidItem(): void
    {
        $item1 = 1;
        $item2 = 2;

        $collection = TypedCollectionImmutable::create('int', [$item1]);

        $newCollection = $collection->add($item2);

        self::assertSame([$item1], $collection->toArray());
        self::assertSame([$item1, $item2], $newCollection->toArray());
    }

    public function testFilter(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

        $newCollection = $collection->filter(
            static function (int $item): bool {
                return $item % 2 === 0;
            }
        );

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item2], $newCollection->toArray());
    }

    public function testReverse(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

        $newCollection = $collection->reverse();

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item3, $item2, $item1], $newCollection->toArray());
    }

    public function testSort(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item3, $item2]);

        $newCollection = $collection->sort(
            static function (int $itemA, int $itemB): int {
                return $itemB <=> $itemA;
            }
        );

        self::assertSame([$item1, $item3, $item2], $collection->toArray());
        self::assertSame([$item3, $item2, $item1], $newCollection->toArray());
    }

    public function testMap(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

        $newCollection = $collection->map(
            static function (int $item): string {
                return "item$item";
            }
        );

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame(['item1', 'item2', 'item3'], $newCollection->toArray());
    }

    public function testMapReturningObjectCollection(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

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

    public function testShuffle(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

        $newCollection = $collection->shuffle();

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame(3, $newCollection->count());
        self::assertTrue($newCollection->contains($item1));
        self::assertTrue($newCollection->contains($item2));
        self::assertTrue($newCollection->contains($item3));
    }

    public function testMerge(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection1 = TypedCollectionImmutable::create('int', [$item1]);
        $collection2 = TypedCollectionImmutable::create('int', [$item2, $item3]);

        $newCollection = $collection1->merge($collection2);

        self::assertSame([$item1], $collection1->toArray());
        self::assertSame([$item2, $item3], $collection2->toArray());
        self::assertSame([$item1, $item2, $item3], $newCollection->toArray());
    }

    public function testMergeWithInvalidValues(): void
    {
        $item1 = 1;
        $item2 = '2';
        $item3 = '3';

        $collection1 = TypedCollectionImmutable::create('int', [$item1]);
        $collection2 = TypedCollectionImmutable::create('string', [$item2, $item3]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: integer');

        $collection1->merge($collection2);
    }

    public function testInsertAtWithInvalidIndex(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;
        $item4 = 4;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Index out of collection bounds');

        $collection->insertAt(3, $item4);
    }

    public function testInsertAtWithInvalidItem(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: integer');

        $collection->insertAt(2, 1.0);
    }

    public function testInsertAt(): void
    {
        $item0 = 0;
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

        $newCollection = $collection->insertAt(0, $item0);

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item0, $item1, $item2, $item3], $newCollection->toArray());
    }

    public function testRemoveAtWithInvalidIndex(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Index out of collection bounds');

        $collection->removeAt(3);
    }

    public function testRemoveAt(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

        $newCollection = $collection->removeAt(1);

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item1, $item3], $newCollection->toArray());
    }

    public function testDropFirst(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

        $newCollection = $collection->dropFirst();

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item2, $item3], $newCollection->toArray());
    }

    public function testDropLast(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollectionImmutable::create('int', [$item1, $item2, $item3]);

        $newCollection = $collection->dropLast();

        self::assertSame([$item1, $item2, $item3], $collection->toArray());
        self::assertSame([$item1, $item2], $newCollection->toArray());
    }
}
