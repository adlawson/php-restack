<?php

namespace Restack;

use ArrayIterator;
use Restack\Exception\InvalidItemException;

/**
 * Simple item storage
 * 
 * @category  Restack
 * @package   Restack
 */
class Index implements \Countable, \IteratorAggregate
{
    const STATE_CLEAN   = 0;
    const STATE_DIRTY   = 1;
    const STATE_CORRUPT = 2;
    
    /**
     * Stored items
     * @var array 
     */
    private $items = array();
    
    /**
     * The index state
     * @var integer
     */
    private $state = self::STATE_CLEAN;
    
    /**
     * Clear storage
     * @return void
     */
    public function clear()
    {
        $this->items = array();
        $this->setState(self::STATE_CLEAN);
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
     * Check if an item exists in the index
     * @param mixed $item
     * @return boolean
     */
    public function exists($item)
    {
        return (false !== $this->search($item));
    }
    
    /**
     * Insert an item into the index
     * @param mixed $item
     * @return void
     */
    public function insert($item)
    {
        if ($this->exists($item))
        {
            throw new InvalidItemException('Item already exists in storage');
        }
        
        $this->setState(self::STATE_DIRTY);
        $this->items[] = $item;
    }
    
    /**
     * Remove an item from the index
     * @param mixed $item
     * @return void
     */
    public function remove($item)
    {
        $key = $this->search($item);
        
        if (false === $key)
        {
            throw new InvalidItemException('Item does not exist in storage');
        }
        
        $this->setState(self::STATE_DIRTY);
        unset($this->items[$key]);
    }
    
    /**
     * Search for an item in storage
     * @param mixed $item
     * @return integer|false Item key or false if not found
     */
    public function search($item)
    {
        return array_search($item, $this->items, true);
    }
    
    /**
     * Get the item index
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
    
    /**
     * Set the item index
     * @param array $items
     * @return void
     */
    public function setItems(array $items)
    {
        $this->clear();
        
        foreach ($items as $item)
        {
            $this->insert($item);
        }
        
        $this->setState(self::STATE_CLEAN);
    }
    
    /**
     * Get the index iterator
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getItems());
    }
    
    /**
     * Get the index state
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the index state
     * @param integer $state
     * @return void
     */
    public function setState($state)
    {
        $this->state = (int) $state;
    }
}