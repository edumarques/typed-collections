<?php

declare(strict_types=1);

namespace EduardoMarques\TypedCollections\Tests\Unit;

use EduardoMarques\TypedCollections\Exception\InvalidArgumentException;
use EduardoMarques\TypedCollections\Exception\OutOfRangeException;
use EduardoMarques\TypedCollections\TypedCollection;
use EduardoMarques\TypedCollections\TypedDictionaryImmutable;
use PHPUnit\Framework\TestCase;

final class TypedCollectionTest extends TestCase
{
    public function testCreateWithInvalidCallableItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value must be callable');

        TypedCollection::create('callable', ['test']);
    }

    public function testCreateWithInvalidObjectItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not type or subtype of Traversable');

        TypedCollection::create(\Traversable::class, [new \stdClass()]);
    }

    public function testCreateWithInvalidNonObjectItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: stdClass');

        TypedCollection::create(\stdClass::class, ['test']);
    }

    public function testCreateWithInvalidNonTypeItem(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('This type is not supported or does not exist');

        TypedCollection::create('object');
    }

    public function testContainsWhenItDoesNot(): void
    {
        $item1 = new \stdClass();
        $collection = TypedCollection::create(\stdClass::class, [$item1]);

        $item2 = new \stdClass();

        self::assertFalse($collection->contains($item2));
    }

    public function testContainsWhenItDoes(): void
    {
        $item1 = new \stdClass();
        $collection = TypedCollection::create(\stdClass::class, [$item1]);

        self::assertTrue($collection->contains($item1));
    }

    public function testAtWithInvalidIndex(): void
    {
        $collection = TypedCollection::create('integer');

        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Index out of collection bounds');

        $collection->at(0);
    }

    public function testAtWithValidIndex(): void
    {
        $item1 = 'test';
        $collection = TypedCollection::create('string', [$item1]);

        self::assertSame($item1, $collection->at(0));
    }

    public function testSlice(): void
    {
        $item1 = 'test1';
        $item2 = 'test2';
        $item3 = 'test3';
        $item4 = 'test4';

        $collection = TypedCollection::create('string', [$item1, $item2, $item3, $item4]);

        $expected = TypedCollection::create('string', [$item2, $item3]);

        self::assertEquals($expected, $collection->slice(1, 2));
    }

    public function testReduce(): void
    {
        $item1 = 1.5;
        $item2 = 1.0;
        $item3 = 3.0;
        $item4 = 45.5;

        $collection = TypedCollection::create('float', [$item1, $item2, $item3, $item4]);

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

        $collection = TypedCollection::create('float', [$item1, $item2, $item3, $item4]);

        self::assertSame(0, $collection->firstIndex());
    }

    public function testFirstIndexWhenCollectionIsEmpty(): void
    {
        $collection = TypedCollection::create('float');

        self::assertNull($collection->firstIndex());
    }

    public function testLastIndex(): void
    {
        $item1 = 1.5;
        $item2 = 1.0;
        $item3 = 3.0;
        $item4 = 45.5;

        $collection = TypedCollection::create('float', [$item1, $item2, $item3, $item4]);

        self::assertSame(3, $collection->lastIndex());
    }

    public function testLastIndexWhenCollectionIsEmpty(): void
    {
        $collection = TypedCollection::create('float');

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

        $collection = TypedCollection::create('callable', [$item1, $item2]);

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

        $collection = TypedCollection::create('callable', [$item1, $item2]);

        self::assertSame($item2, $collection->last());
    }

    public function testCount(): void
    {
        $item1 = 1;
        $item2 = 2;

        $collection = TypedCollection::create('int', [$item1, $item2]);

        self::assertSame(2, $collection->count());
    }

    public function testClear(): void
    {
        $item1 = 1;
        $item2 = 2;

        $collection = TypedCollection::create('int', [$item1, $item2]);

        $collection->clear();

        self::assertSame([], $collection->toArray());
    }

    public function testAddWithInvalidItem(): void
    {
        $item1 = 1;
        $item2 = 2.0;

        $collection = TypedCollection::create('int', [$item1]);

        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Value is not of type: integer');

        $collection->add($item2);
    }

    public function testAddWithValidItem(): void
    {
        $item1 = 1;
        $item2 = 2;

        $collection = TypedCollection::create('int', [$item1]);

        $collection->add($item2);

        self::assertSame([$item1, $item2], $collection->toArray());
    }

    public function testFilter(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

        $collection->filter(
            static function (int $item): bool {
                return $item % 2 === 0;
            }
        );

        self::assertSame([$item2], $collection->toArray());
    }

    public function testReverse(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

        $collection->reverse();

        self::assertSame([$item3, $item2, $item1], $collection->toArray());
    }

    public function testSort(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollection::create('int', [$item1, $item3, $item2]);

        $collection->sort(
            static function (int $itemA, int $itemB): int {
                return $itemB <=> $itemA;
            }
        );

        self::assertSame([$item3, $item2, $item1], $collection->toArray());
    }

    public function testMap(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

        $collection->map(
            static function (int $item): string {
                return "item$item";
            }
        );

        self::assertSame(['item1', 'item2', 'item3'], $collection->toArray());
    }

    public function testMapReturningObjectCollection(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

        $collection->map(
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

        self::assertEquals([$mappedItem1, $mappedItem2, $mappedItem3], $collection->toArray());
    }

    public function testShuffle(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

        $collection->shuffle();

        self::assertSame(3, $collection->count());
        self::assertTrue($collection->contains($item1));
        self::assertTrue($collection->contains($item2));
        self::assertTrue($collection->contains($item3));
    }

    public function testMerge(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection1 = TypedCollection::create('int', [$item1]);
        $collection2 = TypedCollection::create('int', [$item2, $item3]);

        $collection1->merge($collection2);

        self::assertEquals([$item1, $item2, $item3], $collection1->toArray());
    }

    public function testMergeWithInvalidValues(): void
    {
        $item1 = 1;
        $item2 = '2';
        $item3 = '3';

        $collection1 = TypedCollection::create('int', [$item1]);
        $collection2 = TypedCollection::create('string', [$item2, $item3]);

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

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Index out of collection bounds');

        $collection->insertAt(3, $item4);
    }

    public function testInsertAtWithInvalidItem(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

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

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

        $collection->insertAt(0, $item0);

        self::assertSame([$item0, $item1, $item2, $item3], $collection->toArray());
    }

    public function testRemoveAtWithInvalidIndex(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

        self::expectException(OutOfRangeException::class);
        self::expectExceptionMessage('Index out of collection bounds');

        $collection->removeAt(3);
    }

    public function testRemoveAt(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

        $collection->removeAt(1);

        self::assertSame([$item1, $item3], $collection->toArray());
    }

    public function testDropFirst(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

        $collection->dropFirst();

        self::assertSame([$item2, $item3], $collection->toArray());
    }

    public function testDropLast(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3]);

        $collection->dropLast();

        self::assertSame([$item1, $item2], $collection->toArray());
    }

    public function testFilterIndexes(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;
        $item4 = 4;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3, $item4]);

        $callable = static function (int $index, int $item): bool {
            return ($item * ($index + 1)) % 2 === 0;
        };

        $filtered = TypedCollection::create('int', [1, 3]);

        self::assertEquals($filtered, $collection->filterIndexes($callable));
    }

    public function testFindFirstIndex(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;
        $item4 = 4;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3, $item4]);

        $callable = static function (int $index, int $item): bool {
            return ($item * ($index + 1)) % 2 === 0;
        };

        self::assertSame(1, $collection->findFirstIndex($callable));
    }

    public function testFindFirstIndexWhenItDoesNotMeetCriteria(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;
        $item4 = 4;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3, $item4]);

        $callable = static function (int $index, int $item): bool {
            return $item < 0;
        };

        self::assertNull($collection->findFirstIndex($callable));
    }

    public function testFindLastIndex(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;
        $item4 = 4;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3, $item4]);

        $callable = static function (int $index, int $item): bool {
            return ($item * ($index + 1)) % 2 === 0;
        };

        self::assertSame(3, $collection->findLastIndex($callable));
    }

    public function testFindLastIndexWhenItDoesNotMeetCriteria(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;
        $item4 = 4;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3, $item4]);

        $callable = static function (int $index, int $item): bool {
            return $item < 0;
        };

        self::assertNull($collection->findLastIndex($callable));
    }

    public function testFindFirst(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;
        $item4 = 4;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3, $item4]);

        $callable = static function (int $index, int $item): bool {
            return ($item * ($index + 1)) % 2 === 0;
        };

        self::assertSame(2, $collection->findFirst($callable));
    }

    public function testFindFirstWhenItDoesNotMeetCriteria(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;
        $item4 = 4;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3, $item4]);

        $callable = static function (int $index, int $item): bool {
            return $item < 0;
        };

        self::assertNull($collection->findFirst($callable));
    }

    public function testFindLast(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;
        $item4 = 4;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3, $item4]);

        $callable = static function (int $index, int $item): bool {
            return ($item * ($index + 1)) % 2 === 0;
        };

        self::assertSame(4, $collection->findLast($callable));
    }

    public function testFindLastWhenItDoesNotMeetCriteria(): void
    {
        $item1 = 1;
        $item2 = 2;
        $item3 = 3;
        $item4 = 4;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3, $item4]);

        $callable = static function (int $index, int $item): bool {
            return $item < 0;
        };

        self::assertNull($collection->findLast($callable));
    }

    public function testIsEmptyWhenItIsNotEmpty(): void
    {
        $item1 = 1;

        $collection = TypedCollection::create('int', [$item1]);

        self::assertFalse($collection->isEmpty());
    }

    public function testIsEmptyWhenItIsEmpty(): void
    {
        $collection = TypedCollection::create('int');

        self::assertTrue($collection->isEmpty());
    }

    public function testUnique(): void
    {
        $item1 = 5;
        $item2 = 5;
        $item3 = 3;
        $item4 = 3;

        $collection = TypedCollection::create('int', [$item1, $item2, $item3, $item4]);

        $uniqueCollection = $collection->unique();

        self::assertSame($uniqueCollection, $collection);
        self::assertSame([$item1, $item3], $uniqueCollection->toArray());
    }
}
