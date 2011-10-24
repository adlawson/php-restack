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
     * @return void
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
        $this->getIndex()->insert('c');
        $this->getIndex()->insert('d');
    }
    
    /**
     * Insert different data types
     * @covers Restack\Index::insert()
     */
    public function testInsert()
    {
        $obj = new \stdClass;
        
        $this->getIndex()->insert('1');
        $this->getIndex()->insert(1);
        $this->getIndex()->insert(7E-10);
        $this->getIndex()->insert(false);
        $this->getIndex()->insert(array('test' => 'array'));
        $this->getIndex()->insert($obj);
        
        $this->assertSame($this->getIndex()->getItems(), array(
            'a',
            'b',
            'c',
            'd',
            '1',
            1,
            7E-10,
            false,
            array('test' => 'array'),
            $obj
        ));
    }
    
    /**
     * Remove a single item
     * @covers Restack\Index::remove()
     */
    public function testRemove()
    {
        $this->getIndex()->remove('a');
        
        $items = array('a', 'b', 'c', 'd');
        unset($items[0]);
        
        $this->assertSame($this->getIndex()->getItems(), $items);
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