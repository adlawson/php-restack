<?php

namespace Restack\Test\Queue;

use Restack\Exception\InvalidItemException;
use Restack\Queue\Priority;

class PriorityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The data storage
     * @var Restack\Queue\Priority
     */
    public $queue;
    
    /**
     * Setup the Queue
     * @return void
     */
    public function setUp()
    {
        $this->queue = new Priority;
    }
    
    /**
     * Insert storage items
     * @return void
     */
    public function insert()
    {
        $this->queue->insert('a');
        $this->queue->insert('b');
        $this->queue->insert('c', 0);
        $this->queue->insert('d', 999);
    }
    
    /**
     * @covers Restack\Queue\Priority::insert()
     */
    public function testInsert()
    {
        $this->insert();
        $this->assertSame(4, $this->queue->count());
    }
    
    /**
     * @covers Restack\Queue\Priority::getIterator()
     */
    public function testIterator()
    {
        $this->insert();
        
        $items = array();
        foreach ($this->queue as $item) {
            $items[] = $item;
        }
        
        $this->assertSame(array('d', 'a', 'b', 'c'), $items);
    }
    
    /**
     * @covers Restack\Queue\Priority::getOrder()
     */
    public function testGetOrder()
    {
        $this->insert();
        
        $this->assertSame($this->queue->getOrder('a'), Priority::DEFAULT_ORDER);
        $this->assertSame($this->queue->getOrder('b'), Priority::DEFAULT_ORDER);
        $this->assertSame($this->queue->getOrder('c'), 0);
        $this->assertSame($this->queue->getOrder('d'), 999);
    }
    
    /**
     * @covers Restack\Queue\Priority::setOrder()
     */
    public function testSetOrder()
    {
        $this->insert();
        
        $this->queue->setOrder('c', 1337);
        $this->assertSame($this->queue->getOrder('c'), 1337);
        
        try {
            $this->queue->setOrder('invalid_item', 666);
        } catch (InvalidItemException $e) {
            $this->assertTrue(true);
            return;
        }
        
        $this->fail('Exception Restack\Exception\InvalidItemException expected but not thrown');
    }
    
    /**
     * @covers Restack\Queue\Priority::remove()
     */
    public function testRemove()
    {
        $this->insert();
        
        $this->queue->remove('a');
        $this->assertSame(3, $this->queue->count());
    }
    
    /**
     * @covers Restack\Queue\Priority::remove()
     * @depends testRemove
     */
    public function testClear()
    {
        $this->insert();
        
        $this->queue->clear();
        $this->assertSame(0, $this->queue->count());
    }
}