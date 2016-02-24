<?php

namespace pcrov;

class IteratorStackIteratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \ArrayIterator */
    protected $sub1;

    /** @var  \ArrayIterator */
    protected $sub2;

    /** @var  IteratorStackIterator */
    protected $stack;

    public function setUp()
    {
        $sub1 = new \ArrayIterator([36, 24, 36]);
        $sub2 = new \ArrayIterator(
            [
                "first" => "foo",
                "second" => "bar",
                "third" => "baz"
            ]
        );
        $stack = new IteratorStackIterator();

        $this->stack = $stack;
        $this->sub1 = $sub1;
        $this->sub2 = $sub2;
    }

    public function testInitialState()
    {
        $stack = $this->stack;
        $this->assertFalse($stack->valid());
        $this->assertNull($stack->key());
        $this->assertNull($stack->current());
        $this->assertNull($stack->getInnerIterator());
        $this->assertNull($stack->pop());
    }

    public function testPush()
    {
        $this->assertSame(2, $this->stack->push($this->sub1, $this->sub2));
    }

    public function testGetInnerIterator()
    {
        $stack = $this->stack;
        $sub1 = $this->sub1;
        $sub2 = $this->sub2;

        $stack->push($sub1, $sub2);
        $this->assertSame($sub2, $stack->getInnerIterator());
    }

    public function testPop()
    {
        $stack = $this->stack;
        $sub1 = $this->sub1;
        $sub2 = $this->sub2;

        $stack->push($sub1, $sub2);

        $this->assertSame($sub2, $stack->pop());

        $this->assertSame($sub1, $stack->getInnerIterator());
        $this->assertSame($sub1, $stack->pop());

        $this->assertFalse($stack->valid());
        $this->assertNull($stack->key());
        $this->assertNull($stack->current());
        $this->assertNull($stack->getInnerIterator());
        $this->assertNull($stack->pop());
    }

    public function testIterateUntilEmpty()
    {
        $stack = $this->stack;
        $sub1 = $this->sub1;
        $sub2 = $this->sub2;

        $stack->push($sub1, $sub2);
        $stack->rewind();

        $this->assertTrue($stack->valid());
        $this->assertSame("first", $stack->key());
        $this->assertSame("foo", $stack->current());
        $stack->next();

        $this->assertTrue($stack->valid());
        $this->assertSame("second", $stack->key());
        $this->assertSame("bar", $stack->current());
        $stack->next();

        $this->assertTrue($stack->valid());
        $this->assertSame("third", $stack->key());
        $this->assertSame("baz", $stack->current());
        $stack->next();

        $this->assertTrue($stack->valid());
        $this->assertSame(0, $stack->key());
        $this->assertSame(36, $stack->current());
        $stack->next();

        $this->assertTrue($stack->valid());
        $this->assertSame(1, $stack->key());
        $this->assertSame(24, $stack->current());
        $stack->next();

        $this->assertTrue($stack->valid());
        $this->assertSame(2, $stack->key());
        $this->assertSame(36, $stack->current());
        $stack->next();

        $this->assertFalse($stack->valid());
        $this->assertNull($stack->key());
        $this->assertNull($stack->current());
        $this->assertNull($stack->getInnerIterator());
        $this->assertNull($stack->pop());
    }

    public function testPushMidIteration()
    {
        $stack = $this->stack;
        $sub1 = $this->sub1;
        $sub2 = $this->sub2;

        $stack->push($sub1);
        $stack->rewind();

        $this->assertTrue($stack->valid());
        $this->assertSame(0, $stack->key());
        $this->assertSame(36, $stack->current());
        $stack->next();

        $this->assertTrue($stack->valid());
        $this->assertSame(1, $stack->key());
        $this->assertSame(24, $stack->current());
        $stack->next();

        $stack->push($sub2);

        $this->assertTrue($stack->valid());
        $this->assertSame("first", $stack->key());
        $this->assertSame("foo", $stack->current());
        $stack->next();

        $this->assertTrue($stack->valid());
        $this->assertSame("second", $stack->key());
        $this->assertSame("bar", $stack->current());
        $stack->next();

        $this->assertTrue($stack->valid());
        $this->assertSame("third", $stack->key());
        $this->assertSame("baz", $stack->current());
        $stack->next();

        $this->assertTrue($stack->valid());
        $this->assertSame(2, $stack->key());
        $this->assertSame(36, $stack->current());
        $stack->next();

        $this->assertFalse($stack->valid());
        $this->assertNull($stack->key());
        $this->assertNull($stack->current());
        $this->assertNull($stack->getInnerIterator());
        $this->assertNull($stack->pop());
    }
}
