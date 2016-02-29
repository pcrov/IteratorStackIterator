# IteratorStackIterator

[![Build Status](https://travis-ci.org/pcrov/IteratorStackIterator.svg?branch=master)](https://travis-ci.org/pcrov/IteratorStackIterator)
[![License](https://poser.pugx.org/pcrov/iteratorstackiterator/license)](https://github.com/pcrov/IteratorStackIterator/blob/master/LICENSE)
[![Latest Stable Version](https://poser.pugx.org/pcrov/iteratorstackiterator/v/stable)](https://packagist.org/packages/pcrov/iteratorstackiterator)

Iterates over a stack of iterators, discarding them as they complete.

## Requirements

PHP 7

## Installation

To install with composer:

```sh
composer require pcrov/iteratorstackiterator
```

## Usage

This iterator implements [`OuterIterator`](http://php.net/outeriterator), adding `push()` and `pop()` methods to add and
remove inner iterators, respectively.

`push()` will return the new size of the stack.
`pop()` will return the iterator popped from the top.

The values returned from `key()` and `current()` will always be from the current position of the top iterator on the
stack.

`next()` moves the cursor of the iterator at the top of the stack. If that iterator is no longer valid it is removed, as
are any others that have completed until a valid iterator is found or the stack is empty.

`rewind()` will rewind all iterators left on the stack.

### Example 1

```php
$stack = new \pcrov\IteratorStackIterator();
$stack->push(
    new ArrayIterator([1, 2, 3]),
    new ArrayIterator([4, 5, 6]),
    new ArrayIterator([7, 8, 9])
);

foreach ($stack as $value) {
    echo $value;
}

// output: 789456123
```

---

Iterators can be added to the stack after iteration has already begun. They will *not* automatically be rewound when
added, so you should call `rewind()` on them prior if needed.

### Example 2

```php
$stack = new \pcrov\IteratorStackIterator();
$stack->push(
    new ArrayIterator([1, 2, 3]),
    new ArrayIterator([4, 5, 6])
);
$stack->rewind();

while ($stack->valid()) {
    $value = $stack->current();
    echo $value;
    $stack->next();

    if ($value === 2) {
        $stack->push(new ArrayIterator([7, 8, 9]));
    }
}

// output: 456127893
```

Note that `next()` is called prior to pushing a new iterator, otherwise when the stack ran back down that position would
be repeated (which in this case result would result in an infinite loop.)

---
If an inner iterator's cursor position is manipulated from outside the stack iterator the resulting behavior is
undefined.
