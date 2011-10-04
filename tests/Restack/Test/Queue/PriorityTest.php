<?php

namespace Restack\Test\Queue;

use Restack\Exception\InvalidItemException;
use Restack\Queue\Priority;
use stdClass;

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
        $this->queue->insert('1');
        $this->queue->insert(1);
        $this->queue->insert(7E-10);
        $this->queue->insert(false);
        $this->queue->insert(array('test' => 'array'));
        $this->queue->insert(new stdClass());
        
        $this->assertSame(10, $this->queue->count());
    }
    
    /**
     * @covers Restack\Queue\Priority::getIterator()
     */
    public function testIterator()
    {
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
        $this->queue->remove('a');
        $this->assertSame(3, $this->queue->count());
    }
    
    /**
     * @covers Restack\Queue\Priority::remove()
     * @depends testRemove
     */
    public function testClear()
    {
        $this->queue->clear();
        $this->assertSame(0, $this->queue->count());
    }
}