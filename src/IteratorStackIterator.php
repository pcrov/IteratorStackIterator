<?php declare(strict_types = 1);

namespace pcrov;

use Ds\Vector;

/**
 * Class IteratorStackIterator
 *
 * This outer iterator iterates seamlessly over a stack of iterators. If one of
 * the iterators on the stack has its cursor position changed externally the
 * behavior is undefined.
 */
class IteratorStackIterator implements \OuterIterator
{
    /**
     * @var Vector
     */
    private $stack;

    public function __construct()
    {
        $this->stack = new Vector();
    }

    /**
     * @param \Iterator $iterator
     * @param \Iterator ...$moreIterators
     * @return int
     */
    public function push(\Iterator $iterator, \Iterator ...$moreIterators) : int
    {
        $this->stack->push($iterator, ...$moreIterators);
        return $this->stack->count();
    }

    /**
     * @return \Iterator|null
     */
    public function pop()
    {
        return $this->valid() ? $this->stack->pop() : null;
    }

    /**
     * @return mixed the current element from the current iterator
     */
    public function current()
    {
        return $this->valid() ? $this->stack->last()->current() : null;
    }

    /**
     * Move forward to next element.
     *
     * Inner iterators will be removed from the stack automatically as they complete.
     *
     * @return void
     */
    public function next()
    {
        if (!$this->stack->isEmpty()) {
            $this->stack->last()->next();
            $this->discardInvalid();
        }
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->valid() ? $this->stack->last()->key() : null;
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid() : bool
    {
        return !$this->stack->isEmpty();
    }

    /**
     * Rewinds all iterators
     *
     * @return void
     */
    public function rewind()
    {
        foreach ($this->stack as $iterator) {
            $iterator->rewind();
        }
    }

    /**
     * @return \Iterator|null The current iterator
     */
    public function getInnerIterator()
    {
        return $this->valid() ? $this->stack->last() : null;
    }

    /**
     * Removes all invalid iterators from the top of the stack.
     * @return void
     */
    private function discardInvalid()
    {
        while ($this->valid() && !$this->stack->last()->valid()) {
            $this->pop();
        }
    }
}
