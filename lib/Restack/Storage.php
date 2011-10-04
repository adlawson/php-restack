<?php

namespace Restack;

use Restack\Exception\InvalidItemException;

/**
 * Typical item storage interface
 * 
 * @category  Restack
 * @package   Restack\Storage
 */
abstract class Storage implements \Countable, \IteratorAggregate
{
    const STATE_UNSORTED = 0;
    const STATE_SORTED   = 1;
    const STATE_CORRUPT  = 2;
    
    /**
     * Stored items
     * @var array 
     */
    private $items = array();  
    
    /**
     * The storage state
     * @var integer
     */
    private $state = self::STATE_SORTED;
    
    /**
     * Clear storage
     * @return void
     */
    public function clear()
    {
        $this->setItems(array());
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
     * Check if an item exists in storage
     * @param mixed $item
     * @return boolean
     */
    public function exists($item)
    {
        return (bool) array_search($item, $this->items);
    }
    
    /**
     * Insert an item into storage
     * @param mixed $item
     * @return void
     */
    public function insert($item)
    {
        if ($this->exists($item))
        {
            throw new InvalidItemException('Item already exists in storage');
        }
        
        $this->setState(self::STATE_UNSORTED);
        $this->items[] = $item;
    }
    
    /**
     * Remove an item from storage
     * @param mixed $item
     * @return void
     */
    public function remove($item)
    {
        $key = $this->search($item);
        
        $this->setState(self::STATE_UNSORTED);
        unset($this->items[$key]);
    }
    
    /**
     * Search for an item in storage
     * @param mixed $item
     * @throws Restack\Exception\InvalidItemException
     * @return integer The item index
     */
    public function search($item)
    {
        $key = array_search($item, $this->items);
        
        if (false === $key)
        {
            throw new InvalidItemException('Item does not exist in storage');
        }
        
        return $key;
    }
    
    /**
     * Get the item storage
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }
    
    /**
     * Set the item storage
     * @param array $items
     * @return void
     */
    private function setItems(array $items)
    {
        $this->items = $items;
    }
    
    /**
     * Get the storage state
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the storage state
     * @param integer $state
     * @return void
     */
    public function setState($state)
    {
        $this->state = (int) $state;
    }
}