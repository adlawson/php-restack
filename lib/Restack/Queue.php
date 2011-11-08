<?php

namespace Restack;

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
class Queue extends Index
{
    const DEFAULT_ORDER = 1;
    
    /**
     * Item base index for normalising order
     * @var integer
     */
    private $base = PHP_INT_MAX;
    
    /**
     * Map an item to a given priority
     * @var array
     */
    private $priorities = array();
    
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
        $this->queue = null;
        $this->priorities = array();
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
            unset($this->priorities[$key]);
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
        
        return reset($this->priorities[$key]);
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
        
        $this->priorities[$key] = array((int) $order, $this->base--);
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
                $this->queue->insert($item, $this->priorities[$key]);
            }
        }
        
        return $this->queue;
    }
}