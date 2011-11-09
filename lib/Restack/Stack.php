<?php

namespace Restack;

use Restack\Exception\InvalidItemException;
use Restack\Structure\Sortable;
use SplPriorityQueue;

/**
 * Stacked datastructure.
 * Items added to the stack are accessed in LIFO
 * order (Last In, First Out)
 * 
 * @category  Restack
 * @package   Restack
 */
class Stack extends Index implements Sortable
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
     * The stack instance
     * @var SplPriorityQueue
     */
    private $stack;
    
    /**
     * Clear the queue
     * @return void
     */
    public function clear()
    {
        parent::clear();
        
        $this->base  = PHP_INT_MAX;
        $this->order = array();
        $this->stack = null;
    }
    
    /**
     * Add an element to the stack
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
     * Remove an element from the stack
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
     * Get a cloned stack instance for iterating
     * @return SplPriorityQueue
     */
    public function getIterator()
    {
        return clone $this->getStack();
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
        
        return PHP_INT_MAX - reset($this->order[$key]);
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
        
        $this->order[$key] = array((PHP_INT_MAX - (int) $order), $this->base--);
    }
    
    /**
     * Get the stack instance
     * @return SplPriorityQueue
     */
    public function getStack()
    {
        if (self::STATE_DIRTY === $this->getState())
        {
            $this->stack = new SplPriorityQueue;
            
            foreach ($this->getItems() as $key => $item)
            {
                $this->stack->insert($item, $this->order[$key]);
            }
        }
        
        return $this->stack;
    }
}