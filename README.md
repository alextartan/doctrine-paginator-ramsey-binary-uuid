# Paginator for Doctrine ORM that supports `uuid_binary` ids. 

Doctrine's Paginator fails to return objects if their id is of `uuid_binary` type.

This happens because the database won't match a `BINARY(16)` to a string.

The solution was to get those ids (which were converted by `UuidInterface::__toString`) 
and return them with `UuidInterface::getBytes`. This allows for a proper match at the 
database level.

Current build status
===

[![Build Status](https://travis-ci.org/alextartan/doctrine-paginator-ramsey-binary-uuid.svg?branch=master)](https://travis-ci.org/alextartan/doctrine-paginator-ramsey-binary-uuid)
[![Coverage Status](https://coveralls.io/repos/github/alextartan/doctrine-paginator-ramsey-binary-uuid/badge.svg?branch=master)](https://coveralls.io/github/alextartan/doctrine-paginator-ramsey-binary-uuid?branch=master)
[![Mutation testing badge](https://badge.stryker-mutator.io/github.com/alextartan/doctrine-paginator-ramsey-binary-uuid/master)](https://stryker-mutator.github.io)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alextartan/doctrine-paginator-ramsey-binary-uuid/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alextartan/doctrine-paginator-ramsey-binary-uuid/?branch=master)
[![Downloads](https://img.shields.io/badge/dynamic/json.svg?url=https://repo.packagist.org/packages/alextartan/doctrine-paginator-ramsey-binary-uuid.json&label=Downloads&query=$.package.downloads.total&colorB=orange)](https://packagist.org/packages/alextartan/doctrine-paginator-ramsey-binary-uuid)

## Installation

```bash
composer require alextartan/doctrine-paginator-ramsey-binary-uuid
```

## Usage

```php
$repo = $this->em->getRepository(SomeEntity::class);
$query = $repo->createQueryBuilder('se')
              ->setMaxResults(2)
              // more stuff here
              ->getQuery();

$p = new BinaryUuidSafePaginator($query);
```

## Versioning

This library adheres to [semver](https://semver.org/)