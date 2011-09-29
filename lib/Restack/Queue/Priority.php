<?php

namespace Restack\Queue;

use Restack\Storage;
use Restack\InvalidItemException;
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
class Priority implements Storage
{
    const DEFAULT_ORDER = 1;
    
    /**
     * Item index for normalising order
     * @var integer
     */
    protected $index = PHP_INT_MAX;
    
    /**
     * A temp storage of items to queue
     * @var array
     */
    protected $items = array();
    
    /**
     * The queue
     * @var SplPriorityQueue
     */
    protected $queue;
    
    /**
     * Clear the queue and reset internal values
     * @return void
     */
    public function clear()
    {
        $this->index = PHP_INT_MAX;
        $this->items = array();
        $this->queue = null;
    }
    
    /**
     * Count the queue items
     * @return integer
     */
    public function count()
    {
        return count($this->items);
    }
    
    /**
     * Check an item exists in the queue
     * @param mixed $item
     * @return boolean
     */
    public function exists($item)
    {
        foreach ($this->items as $value) {
            if ($value['data'] === $item) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Add an element to the queue
     * @param mixed $item
     * @param integer $priority
     * @return void
     */
    public function insert($item, $priority = self::DEFAULT_ORDER)
    {
        $priority = array($priority, $this->index--);
        
        $this->queue = null;
        
        $this->items[] = array(
            'data'     => $item,
            'priority' => $priority
        );
    }
    
    /**
     * Remove an element from the queue
     * @param mixed $item
     * @return void
     */
    public function remove($item)
    {
        $exists = false;
        foreach ($this->items as $key => $value) {
            if ($item === $value['data']) {
                $exists = true;
                break;
            }
        }
        
        if ($exists) {
            unset($this->items[$key]);
            $this->queue = null;
        }
    }
    
    /**
     * Get the queue instance
     * @return SplPriorityQueue
     */
    public function getQueue()
    {
        if (null === $this->queue) {
            $this->queue = new SplPriorityQueue;
            
            foreach ($this->items as $item) {
                $this->queue->insert($item['data'], $item['priority']);
            }
        }
        
        return $this->queue;
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
     * @return integer|null
     */
    public function getOrder($item)
    {
        foreach ($this->items as $value) {
            if ($value['data'] === $item) {
                return current($value['priority']);
            }
        }
        
        return null;
    }
    
    /**
     * Set the priority of an existing item
     * 
     * This is done by simply removing the item and
     * reinserting with the new priority.
     * 
     * @param mixed $item
     * @param integer $priority
     * @throws Restack\InvalidItemException
     * @return Restack\Queue\Priority
     */
    public function setOrder($item, $priority)
    {
        if ($this->exists($item)) {
            $this->remove($item);
            $this->insert($item, $priority);
            return $this;
        }
        
        throw new InvalidItemException('Can\'t set priority on a non-existent item');
    }
}