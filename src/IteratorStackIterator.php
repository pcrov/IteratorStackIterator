<?php declare(strict_types = 1);

namespace pcrov;

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
     * @var \Iterator[]
     */
    private $stack = [];

    /**
     * @var \Iterator|null Top of the stack.
     */
    private $top;

    /**
     * @param \Iterator $iterator
     * @param \Iterator ...$moreIterators
     * @return int
     */
    public function push(\Iterator $iterator, \Iterator ...$moreIterators) : int
    {
        $count = array_push($this->stack, $iterator, ...$moreIterators);
        $this->top = end($this->stack);
        return $count;
    }

    /**
     * @return \Iterator|null
     */
    public function pop()
    {
        $popped = array_pop($this->stack);
        $this->top = end($this->stack) ?: null;
        return $popped;
    }

    /**
     * @return mixed the current element from the current iterator
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
        $top = $this->top;
        if ($top !== null) {
            $top->next();
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
     * @return \Iterator|null The current iterator
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
