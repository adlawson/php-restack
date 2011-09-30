<?php

namespace Restack\Queue;

use Restack\Exception\CircularDependencyException;
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
class Priority implements Storage
{
    const DEFAULT_ORDER = 1;
    
    /**
     * Dependency map
     * @var array
     */
    protected $dependencies;
    
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
            'data'      => $item,
            'listeners' => array(),
            'priority'  => $priority
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
     * @throws Restack\Exception\InvalidItemException
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
    
    /**
     * Add an item dependency
     * @param mixed $target
     * @param mixed $item
     * @throws Restack\Exception\InvalidItemException
     * @throws Restack\Exception\CircularDependencyException
     * @throws Restack\Exception\InvalidItemException
     * @return void
     */
    public function addDependency($target, $item)
    {
        if (!$this->exists($item)) {
            throw new InvalidItemException('Can\'t track a dependency for a non-existent item');
        }
        
        $exists = false;
        foreach ($this->items as $key => $value) {
            if ($value['data'] === $item && in_array($target, $value['listeners'])) {
                throw new CircularDependencyException('Can\'t create circular or recursive dependencies');
            }
            
            if ($value['data'] === $target) {
                $exists = true;
                break;
            }
        }
        
        if (!exists) {
            throw new InvalidItemException('Can\'t add a dependency listener to a non-existent target');
        } elseif (!in_array($item, $value['listeners'])) {
            $this->items[$key]['listeners'][] = $item;
        }
    }
}