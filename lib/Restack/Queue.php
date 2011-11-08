<?php

namespace Restack;

use Restack\Dependency\Trackable;
use Restack\Exception\InvalidItemException;
use SplPriorityQueue;

/**
 * Queued datastructure.
 * Items added to the queue are accessed in FIFO
 * order (First In, First Out)
 * 
 * @category  Restack
 * @package   Restack
 */
class Queue extends Index implements Trackable
{
    const DEFAULT_ORDER = 1;
    
    /**
     * Item base index for normalising order
     * @var integer
     */
    private $base = PHP_INT_MAX;
    
    /**
     * Map an item to a given position
     * @var array
     */
    private $order = array();
    
    /**
     * The queue instance
     * @var SplPriorityQueue
     */
    private $queue;
    
    /**
     * Clear the queue
     * @return void
     */
    public function clear()
    {
        parent::clear();
        
        $this->base  = PHP_INT_MAX;
        $this->order = array();
        $this->queue = null;
    }
    
    /**
     * Add an element to the queue
     * @param mixed $item
     * @param integer $priority
     * @throws Restack\Exception\InvalidItemException
     * @return void
     */
    public function insert($item, $priority = self::DEFAULT_ORDER)
    {
        parent::insert($item);
        $this->setOrder($item, $priority);
    }
    
    /**
     * Remove an element from the queue
     * @param mixed $item
     * @throws Restack\Exception\InvalidItemException
     * @return void
     */
    public function remove($item)
    {
        $key = $this->search($item);
        
        if (false !== $key)
        {
            unset($this->order[$key]);
        }
        
        parent::remove($item);
    }
    
    /**
     * Get a cloned queue instance for iterating
     * @return SplPriorityQueue
     */
    public function getIterator()
    {
        return clone $this->getQueue();
    }
    
    /**
     * Get the priority of an item
     * @param mixed $item
     * @throws Restack\Exception\InvalidItemException
     * @return integer
     */
    public function getOrder($item)
    {
        $key = $this->search($item);
        
        if (false === $key)
        {
            throw new InvalidItemException('Item does not exist in storage');
        }
        
        return reset($this->order[$key]);
    }
    
    /**
     * Set the priority of an item
     * @param mixed $item
     * @param integer $order
     * @throws Restack\Exception\InvalidItemException
     * @return void
     */
    public function setOrder($item, $order)
    {
        $key = $this->search($item);
        
        if (false === $key)
        {
            throw new InvalidItemException('Item does not exist in storage');
        }
        
        $this->order[$key] = array((int) $order, $this->base--);
    }
    
    /**
     * Get the queue instance
     * @return SplPriorityQueue
     */
    public function getQueue()
    {
        if (self::STATE_UNSORTED === $this->getState())
        {
            $this->queue = new SplPriorityQueue;
            
            foreach ($this->getItems() as $key => $item)
            {
                $this->queue->insert($item, $this->order[$key]);
            }
        }
        
        return $this->queue;
    }
}