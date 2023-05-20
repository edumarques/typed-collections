Typed Collections
================
[![edumarques](https://circleci.com/gh/edumarques/typed-collections.svg?style=svg)](https://app.circleci.com/pipelines/github/edumarques)

## Description

This library contains four collection classes: mutable and immutable lists, and mutable and immutable dictionaries. Here we have the flexibility of creating mutable lists, i.e., whenever we perform an operation on its internal array, the same is altered. Or we can simply use the immutable lists, which generate a new instance of the collection class when operations are done on it.

## Requirements

- v1.x
  - Requires PHP 7.1 or greater.

## Collection

A PHP implementation of an array list. Its type is specified at instantiation/construction. The class will perform runtime type checks to validate the appropriate values are being added. Many of the standard array functionalities are encapsulated in this class.

## Dictionary

Dictionaries extend Collections' functionalities. The main difference is that they work with associative arrays, where you map keys to values.

## Supported types:

- int or integer 
- bool or boolean
- float or double
- array
- object
- callable

## Support for abstract classes and interfaces

Collections and Dictionaries will check inheritance, so if you require a base class, derived classes can be added safely.
