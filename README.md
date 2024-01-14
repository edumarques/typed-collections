Typed Collections
================
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/edumarques/typed-collections/php?version=v2.x-dev&color=%23777BB3)
![GitHub](https://img.shields.io/github/license/edumarques/typed-collections)
[![edumarques](https://circleci.com/gh/edumarques/typed-collections.svg?style=shield)](https://app.circleci.com/pipelines/github/edumarques)
[![codecov](https://codecov.io/gh/edumarques/typed-collections/branch/main/graph/badge.svg?token=ABGMyvr355)](https://codecov.io/gh/edumarques/typed-collections)

## Description

Typed collections are an implementation of typed lists. Collections allow us to have traversable lists of a predetermined type and its subtypes. Its behavior prevents the looseness of arrays in PHP.

This library contains four collection classes: mutable and immutable lists, and mutable and immutable dictionaries. Mutable lists/dictionaries have its internal array altered whenever we perform operations on it. Immutable lists/dictionaries generate a new instance of itself when operations are done on it.

## Installation

```
composer require edumarques/typed-collections:2.*
```

## Basic usage

```php
use EduardoMarques\TypedCollections\TypedCollection;
use EduardoMarques\TypedCollections\TypedCollectionImmutable;
use EduardoMarques\TypedCollections\Enum\ScalarType;
use EduardoMarques\TypedCollections\Enum\NonScalarType;

$typedCollection1 = TypedCollection::create(ScalarType::INTEGER);
$typedCollection2 = TypedCollection::create(ScalarType::STRING);

$typedCollectionImmutable1 = TypedCollectionImmutable::create(NonScalarType::CALLABLE);
$typedCollectionImmutable2 = TypedCollectionImmutable::create(\stdClass::class);
```

```php
use EduardoMarques\TypedCollections\TypedDictionary;
use EduardoMarques\TypedCollections\TypedDictionaryImmutable;
use EduardoMarques\TypedCollections\Enum\ScalarType;
use EduardoMarques\TypedCollections\Enum\NonScalarType;

$typedDictionary1 = TypedDictionary::create(ScalarType::INTEGER, ScalarType::STRING);
$typedDictionary2 = TypedDictionary::create(ScalarType::STRING, ScalarType::DOUBLE);

$typedDictionaryImmutable1 = TypedDictionaryImmutable::create(ScalarType::INTEGER, NonScalarType::CALLABLE);
$typedDictionaryImmutable2 = TypedDictionaryImmutable::create(ScalarType::STRING, \stdClass::class);
```

## Collection

A PHP implementation of an array list. Its type is specified at instantiation/construction. The class will perform runtime type checks to validate the appropriate values are being added. Many of the standard array functionalities are encapsulated in this class.

## Dictionary

Dictionaries extend Collections' functionalities. The main difference is that they work with associative arrays, where you map keys to values.

## Supported types:

- `ScalarType::INTEGER`
- `ScalarType::STRING`
- `ScalarType::BOOLEAN`
- `ScalarType::DOUBLE`
- `NonScalarType::ARRAY`
- `NonScalarType::CALLABLE`
- `class-string`

## Support for abstract classes and interfaces

Collections and Dictionaries will check inheritance, so if you require a base class, derived classes can be added safely.

## Contributing

Contributors are always welcome! For more information on how you can contribute, please read our [contribution guideline](CONTRIBUTING.md).
