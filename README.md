Typed Collections
================
![GitHub](https://img.shields.io/github/license/edumarques/typed-collections)
![Packagist Version](https://img.shields.io/packagist/v/edumarques/typed-collections)
[![edumarques](https://circleci.com/gh/edumarques/typed-collections.svg?style=shield)](https://app.circleci.com/pipelines/github/edumarques)
[![codecov](https://codecov.io/gh/edumarques/typed-collections/branch/main/graph/badge.svg?token=ABGMyvr355)](https://codecov.io/gh/edumarques/typed-collections)

## Description

Typed collections are an implementation of typed lists. Collections allow us to have traversable lists of a predetermined type and its subtypes. Its behavior prevents the looseness of arrays in PHP.

This library contains four collection classes: mutable and immutable lists, and mutable and immutable dictionaries. Mutable lists/dictionaries have its internal array altered whenever we perform operations on it. Immutable lists/dictionaries generate a new instance of itself when operations are done on it.

## Installation

```
composer require edumarques/typed-collections
```

## Basic usage

```php
use EduardoMarques\TypedCollections\TypedCollection;
use EduardoMarques\TypedCollections\TypedCollectionImmutable;

$typedCollection1 = TypedCollection::create('int');
$typedCollection2 = TypedCollection::create('string');

$typedCollectionImmutable1 = TypedCollectionImmutable::create('callable');
$typedCollectionImmutable2 = TypedCollectionImmutable::create(\stdClass::class);
```

```php
use EduardoMarques\TypedCollections\TypedDictionary;
use EduardoMarques\TypedCollections\TypedDictionaryImmutable;

$typedDictionary1 = TypedDictionary::create('int', 'string');
$typedDictionary2 = TypedDictionary::create('string', 'float');

$typedDictionaryImmutable1 = TypedDictionaryImmutable::create('int', 'callable');
$typedDictionaryImmutable2 = TypedDictionaryImmutable::create('string', \stdClass::class);
```

## Requirements

- v2.x
  - Requires PHP 8.0 or greater.

## Collection

A PHP implementation of an array list. Its type is specified at instantiation/construction. The class will perform runtime type checks to validate the appropriate values are being added. Many of the standard array functionalities are encapsulated in this class.

## Dictionary

Dictionaries extend Collections' functionalities. The main difference is that they work with associative arrays, where you map keys to values.

## Supported types:

- int or integer 
- bool or boolean
- float or double
- array
- callable
- class-string

## Support for abstract classes and interfaces

Collections and Dictionaries will check inheritance, so if you require a base class, derived classes can be added safely.

## Contributing

Contributors are always welcome! For more information on how you can contribute, please read our [contribution guideline](CONTRIBUTING.md).
