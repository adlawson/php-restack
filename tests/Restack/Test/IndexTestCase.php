<?php

namespace Restack\Test;

use Restack\Index;

abstract class IndexTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * The index instance
     * @var Restack\Index
     */
    private $index;
    
    /**
     * Get the index instance
     * @return Restack\Index
     */
    public function getIndex()
    {
        return $this->index;
    }
    
    /**
     * Set the index instance
     * @param Restack\Index $index
     */
    public function setIndex(Index $index)
    {
        $this->index = $index;
    }
    
    /**
     * Setup the queue
     * @return void
     */
    public function setUp()
    {
        $this->getIndex()->insert('a');
        $this->getIndex()->insert('b');
        $this->getIndex()->insert('c', 0);
        $this->getIndex()->insert('d', 999);
    }
    
    /**
     * Insert different data types
     * @covers Restack\Index::insert()
     */
    public function testInsert()
    {
        $this->getIndex()->insert('1');
        $this->getIndex()->insert(1);
        $this->getIndex()->insert(7E-10);
        $this->getIndex()->insert(false);
        $this->getIndex()->insert(array('test' => 'array'));
        $this->getIndex()->insert(new \stdClass());
        
        $this->assertSame(10, $this->getIndex()->count());
    }
    
    /**
     * Remove a single item
     * @covers Restack\Index::remove()
     */
    public function testRemove()
    {
        $this->getIndex()->remove('a');
        $this->assertSame(3, $this->getIndex()->count());
    }
    
    /**
     * Clear all items
     * @covers Restack\Index::remove()
     */
    public function testClear()
    {
        $this->getIndex()->clear();
        $this->assertSame(0, $this->getIndex()->count());
    }
}