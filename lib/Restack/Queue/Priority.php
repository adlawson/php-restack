<?php

namespace Restack\Queue;

use Restack\Exception\InvalidItemException;
use Restack\Storage;
use SplPriorityQueue;

/**
 * Reusable, injectable priority ordered queue.
 * Items added to the queue are accessed in queue fashion,
 * meaning; first in, first out.
 * 
 * Items are ordered by priority, so an item with a high
 * priority will be placed nearer the top of the queue.
 * 
 * @category  Restack
 * @package   Restack\Queue
 */
class Priority extends Storage
{
    const DEFAULT_ORDER = 1;
    
    /**
     * Item index for normalising order
     * @var integer
     */
    private $index = PHP_INT_MAX;
    
    /**
     * Map an item to a given priority
     * @var array
     */
    private $map = array();
    
    /**
     * The queue
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
        
        $this->index = PHP_INT_MAX;
        $this->map   = array();
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
        unset($this->map[$key]);
        
        parent::remove($item);
    }
    
    /**
     * Get a cloned queue instance for iterating
     * @param boolean $persist
     * @return SplPriorityQueue
     */
    public function getIterator($persist = true)
    {
        if ($persist) {
            return clone $this->getQueue();
        }
        
        return $this->getQueue();
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
        return current($this->map[$key]);
    }
    
    /**
     * Set the priority of an existing item
     * @param mixed $item
     * @param integer $priority
     * @throws Restack\Exception\InvalidItemException
     * @return void
     */
    public function setOrder($item, $priority)
    {
        $key = $this->search($item);
        $this->map[$key] = array((int) $priority, $this->index--);
    }
    
    /**
     * Get the queue instance
     * @return SplPriorityQueue
     */
    public function getQueue()
    {
        if (self::STATE_UNSORTED === $this->getState()) {
            $this->queue = new SplPriorityQueue;
            
            foreach ($this->getItems() as $key => $item) {
                $this->queue->insert($item, $this->map[$key]);
            }
        }
        
        return $this->queue;
    }
}