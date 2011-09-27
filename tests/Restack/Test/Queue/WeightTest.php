<?php

namespace Restack\Test\Queue;

use Restack\Queue\Weight;
use Restack\Queue\Exception\InvalidItemException;

class WeightTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The data structure
     * @var Restack\Queue\Weight
     */
    public static $queue;
    
    /**
     * Setup the Queue
     */
    public static function setUpBeforeClass()
    {
        self::$queue = new Weight;
    }
    
    /**
     * @covers Restack\Queue\Weight::insert()
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
     * @covers Restack\Queue\Weight::getIterator()
     * @depends testInsert
     */
    public function testIterator()
    {
        $items = array();
        foreach (self::$queue as $item) {
            $items[] = $item;
        }
        
        $this->assertSame($items, array(
            'bar',
            'hello',
            'world',
            'foo'
        ));
    }
    
    /**
     * @covers Restack\Queue\Weight::getOrder()
     * @depends testInsert
     */
    public function testGetOrder()
    {
        $this->assertSame(Weight::DEFAULT_ORDER, self::$queue->getOrder('hello'));
        $this->assertSame(Weight::DEFAULT_ORDER, self::$queue->getOrder('world'));
        $this->assertSame(100, self::$queue->getOrder('foo'));
        $this->assertSame(0, self::$queue->getOrder('bar'));
    }
    
    /**
     * @covers Restack\Queue\Weight::setOrder()
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
        
        $this->fail('Exception Restack\Queue\Exception\InvalidItemException expected but not thrown');
    }
}