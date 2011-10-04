<?php

namespace Restack\Queue;

use Restack\Dependable;
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
class Priority implements Storage, Dependable
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
     * Add an item dependency
     * @param mixed $parent
     * @param mixed $child
     * @return void
     */
    public function addDependency($parent, $child)
    {
        if (!$this->exists($child)) {
            throw new InvalidItemException('Can\'t add a dependency for a non-existent child item');
        }
        
        try {
            $parentKey = $this->getKey($parent);
            $childKey  = $this->getKey($child);
            
            $childPriority = $this->items[$childKey]['priority'];
            
            // Reorder parent item and re-index child
            if ($this->items[$parentKey]['priority'] < $childPriority) {
                $this->setOrder($parent, $childPriority);
                $this->setOrder($child, $childPriority);
            }
        } catch (InvalidItemException $e) {
            throw new InvalidItemException('Can\'t depend on a non-existent parent item');
        }
    }
    
    /**
     * Clear the queue
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
     * @throws Restack\Exception\InvalidItemException
     * @return void
     */
    public function insert($item, $priority = self::DEFAULT_ORDER)
    {
        if ($this->exists($item)) {
            throw new InvalidItemException('Item already exists');
        }
        
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
     * @throws Restack\Exception\InvalidItemException
     * @return void
     */
    public function remove($item)
    {
        try {
            $key = $this->getKey($item);
            
            unset($this->items[$key]);
            $this->queue = null;
        } catch (\Restack\Exception\InvalidItemException $e) {
            throw new InvalidItemException('Can\'t remove non-existent item');
        }
    }
    
    /**
     * Get the item index
     * 
     * This is the key under which the item
     * is stored in the temporary storage, no the
     * queue
     * 
     * @throws Restack\Exception\InvalidItemException
     * @return integer
     */
    protected function getKey($item)
    {
        foreach ($this->items as $key => $value) {
            if ($item === $value['data']) {
                return $key;
            }
        }
        
        throw new InvalidItemException('Can\'t get the key of a non-existent item');
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
}