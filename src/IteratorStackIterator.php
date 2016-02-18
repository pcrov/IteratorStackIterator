<?php declare(strict_types = 1);

namespace pcrov;

use Iterator;

class IteratorStackIterator implements \OuterIterator
{
    /**
     * @var Iterator[]
     */
    private $stack = [];

    /**
     * @var Iterator|null Top of the stack.
     */
    private $top;

    /**
     * @param Iterator $iterator
     * @return void
     */
    public function push(Iterator $iterator)
    {
        $this->stack[] = $this->top = $iterator;
    }

    /**
     * @return Iterator|null
     */
    public function pop()
    {
        $popped = array_pop($this->stack);
        $this->top = end($this->stack) ?: null;
        return $popped;
    }

    /**
     * @return mixed the current element
     */
    public function current()
    {
        if ($this->top === null) {
            return null;
        }
        return $this->top->current();
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
        $this->top->next();
        $this->discardInvalid();
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        if ($this->top === null) {
            return null;
        }
        return $this->top->key();
    }

    /**
     * Checks if current position is valid
     *
     * @return bool
     */
    public function valid() : bool
    {
        return $this->top !== null;
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
     * @return Iterator|null The current iterator
     */
    public function getInnerIterator()
    {
        return $this->top;
    }

    /**
     * Removes all invalid iterators from the top of the stack.
     * @return void
     */
    private function discardInvalid()
    {
        while ($this->top !== null && !$this->top->valid()) {
            $this->pop();
        }
    }
}
