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

## Requirements

- v1.x
  - Requires PHP 7.1 or greater.
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
