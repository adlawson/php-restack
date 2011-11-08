<?php

namespace Restack\Test;

use Restack\Stack;

class StackTest extends IndexTest
{
    /**
     * Setup the Queue
     * @return void
     */
    public function setUp()
    {
        $this->setIndex(new Stack);
        
        $this->getIndex()->insert('a');
        $this->getIndex()->insert('b');
        $this->getIndex()->insert('c', 0);
        $this->getIndex()->insert('d', 999);
    }
    
    /**
     * Check the order from iterator output
     * @covers Restack\Stack::getIterator()
     */
    public function testIterator()
    {
        $items = array();
        foreach ($this->getIndex() as $item)
        {
            $items[] = $item;
        }
        
        $this->assertSame(array('c', 'a', 'b', 'd'), $items);
    }
    
    /**
     * Get weight on valid and invalid items
     * @covers Restack\Stack::getOrder()
     */
    public function testGetOrder()
    {
        $this->assertSame($this->getIndex()->getOrder('a'), Stack::DEFAULT_ORDER);
        $this->assertSame($this->getIndex()->getOrder('b'), Stack::DEFAULT_ORDER);
        $this->assertSame($this->getIndex()->getOrder('c'), 0);
        $this->assertSame($this->getIndex()->getOrder('d'), 999);
        
        $this->setExpectedException('Restack\Exception\InvalidItemException');
        $this->getIndex()->getOrder('invalid_item');
    }
    
    /**
     * Set weight on valid and invalid items
     * @covers Restack\Stack::setOrder()
     */
    public function testSetOrder()
    {
        $this->getIndex()->setOrder('c', 1337);
        $this->assertSame($this->getIndex()->getOrder('c'), 1337);
        
        $this->setExpectedException('Restack\Exception\InvalidItemException');
        $this->getIndex()->setOrder('invalid_item', 666);
    }
}