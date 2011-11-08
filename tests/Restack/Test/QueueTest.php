<?php

namespace Restack\Test;

use Restack\Queue;

class QueueTest extends IndexTest
{
    /**
     * Setup the queue
     * @return void
     */
    public function setUp()
    {
        $this->setIndex(new Queue);
        
        $this->getIndex()->insert('a');
        $this->getIndex()->insert('b');
        $this->getIndex()->insert('c', 0);
        $this->getIndex()->insert('d', 999);
    }
    
    /**
     * Check the order from iterator output
     * @covers Restack\Queue::getIterator()
     */
    public function testIterator()
    {
        $items = array();
        foreach ($this->getIndex() as $item)
        {
            $items[] = $item;
        }
        
        $this->assertSame(array('d', 'a', 'b', 'c'), $items);
    }
    
    /**
     * Get weight on valid and invalid items
     * @covers Restack\Queue::getOrder()
     */
    public function testGetOrder()
    {
        $this->assertSame($this->getIndex()->getOrder('a'), Queue::DEFAULT_ORDER);
        $this->assertSame($this->getIndex()->getOrder('b'), Queue::DEFAULT_ORDER);
        $this->assertSame($this->getIndex()->getOrder('c'), 0);
        $this->assertSame($this->getIndex()->getOrder('d'), 999);
        
        $this->setExpectedException('Restack\Exception\InvalidItemException');
        $this->getIndex()->getOrder('invalid_item');
    }
    
    /**
     * Set weight on valid and invalid items
     * @covers Restack\Queue::setOrder()
     */
    public function testSetOrder()
    {
        $this->getIndex()->setOrder('c', 1337);
        $this->assertSame($this->getIndex()->getOrder('c'), 1337);
        
        $this->setExpectedException('Restack\Exception\InvalidItemException');
        $this->getIndex()->setOrder('invalid_item', 666);
    }
}