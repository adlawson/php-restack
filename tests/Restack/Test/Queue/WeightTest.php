<?php

namespace Restack\Test\Queue;

use Restack\Exception\InvalidItemException;
use Restack\Queue\Weight;

class WeightTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The data storage
     * @var Restack\Queue\Weight
     */
    public $queue;
    
    /**
     * Setup the Queue
     * @return void
     */
    public function setUp()
    {
        $this->queue = new Weight;
        
        $this->queue->insert('a');
        $this->queue->insert('b');
        $this->queue->insert('c', 0);
        $this->queue->insert('d', 999);
    }
    
    /**
     * @covers Restack\Queue\Weight::getIterator()
     */
    public function testIterator()
    {
        $items = array();
        foreach ($this->queue as $item) {
            $items[] = $item;
        }
        
        $this->assertSame(array('c', 'a', 'b', 'd'), $items);
    }
    
    /**
     * @covers Restack\Queue\Weight::getOrder()
     */
    public function testGetOrder()
    {
        $this->assertSame($this->queue->getOrder('a'), Weight::DEFAULT_ORDER);
        $this->assertSame($this->queue->getOrder('b'), Weight::DEFAULT_ORDER);
        $this->assertSame($this->queue->getOrder('c'), 0);
        $this->assertSame($this->queue->getOrder('d'), 999);
    }
    
    /**
     * @covers Restack\Queue\Weight::setOrder()
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
}