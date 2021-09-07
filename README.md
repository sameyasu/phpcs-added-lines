# phpcs-added-lines

This is `phpcs` wrapper for filtering report based on diff text.

- Filters by added/modified line ranges.
- JSON reporting only.

![Test Status](https://github.com/sameyasu/phpcs-added-lines/actions/workflows/test.yaml/badge.svg)

## Installation

```
$ composer require sameyasu/phpcs-added-lines
```

## Usage

- Checking unstaged changes based on PSR12 style

```
$ git diff | ./vendor/bin/phpcs-added-lines --basepath=$(pwd) --standard=PSR12
```
