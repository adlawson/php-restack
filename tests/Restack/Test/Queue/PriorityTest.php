<?php

namespace Restack\Test\Queue;

use Restack\Exception\InvalidItemException;
use Restack\Queue\Priority;

class PriorityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The data structure
     * @var Restack\Queue\Priority
     */
    public static $queue;
    
    /**
     * Setup the Queue
     */
    public static function setUpBeforeClass()
    {
        self::$queue = new Priority;
    }
    
    /**
     * @covers Restack\Queue\Priority::insert()
     */
    public function testInsert()
    {
        self::$queue->insert('hello');
        self::$queue->insert('world');
        self::$queue->insert('foo', 100);
        self::$queue->insert('bar', 0);
        
        $this->assertSame(4, self::$queue->count());
    }
    
    /**
     * @covers Restack\Queue\Priority::getIterator()
     * @depends testInsert
     */
    public function testIterator()
    {
        $items = array();
        foreach (self::$queue as $item) {
            $items[] = $item;
        }
        
        $this->assertSame($items, array(
            'foo',
            'hello',
            'world',
            'bar'
        ));
    }
    
    /**
     * @covers Restack\Queue\Priority::getOrder()
     * @depends testInsert
     */
    public function testGetOrder()
    {
        $this->assertSame(Priority::DEFAULT_ORDER, self::$queue->getOrder('hello'));
        $this->assertSame(Priority::DEFAULT_ORDER, self::$queue->getOrder('world'));
        $this->assertSame(100, self::$queue->getOrder('foo'));
        $this->assertSame(0, self::$queue->getOrder('bar'));
    }
    
    /**
     * @covers Restack\Queue\Priority::setOrder()
     * @depends testGetOrder
     */
    public function testSetOrder()
    {
        self::$queue->setOrder('hello', 9999);
        $this->assertSame(9999, self::$queue->getOrder('hello'));
        
        try {
            self::$queue->setOrder('invalid_item', 666);
        } catch (InvalidItemException $e) {
            $this->assertTrue(true);
            return;
        }
        
        $this->fail('Exception Restack\Exception\InvalidItemException expected but not thrown');
    }
    
    /**
     * @covers Restack\Queue\Priority::remove()
     * @depends testSetOrder
     */
    public function testRemove()
    {
        self::$queue->remove('world');
        $this->assertSame(3, self::$queue->count());
    }
    
    /**
     * @covers Restack\Queue\Priority::addDependency()
     * @depends testRemove
     */
    public function testAddDependency()
    {
        self::$queue->addDependency('bar', 'foo');
        
        $items = array();
        foreach (self::$queue as $item) {
            $items[] = $item;
        }
        
        $this->assertSame($items, array(
            'bar',
            'foo',
            'hello'
        ));
    }
    
    /**
     * @covers Restack\Queue\Priority::remove()
     * @depends testRemove
     */
    public function testClear()
    {
        self::$queue->clear();
        $this->assertSame(0, self::$queue->count());
    }
}