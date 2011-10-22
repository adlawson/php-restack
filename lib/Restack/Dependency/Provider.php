<?php

namespace Restack\Dependency;

use Restack\Index;
use Restack\Exception\CircularDependencyException;
use Restack\Exception\InvalidItemException;

/**
 * Dependency container
 * 
 * @category  Restack
 * @package   Restack\Dependency
 */
class Provider
{
    /**
     * The index instance
     * @var Restack\Index
     */
    private $index;
    
    /**
     * Map of item dependent children
     * @var array
     */
    private $dependencies = array();
    
    /**
     * Clear dependencies
     * @return void
     */
    public function clear()
    {
        $this->dependencies = array();
    }
    
    /**
     * Setup the dependency provider
     * @param Restack\Index $index
     * @return void
     */
    public function __construct(Index $index)
    {
        $this->setIndex($index);
    }
    
    /**
     * Add an item dependency
     * @param mixed $item
     * @param mixed $parent
     * @return void
     */
    public function addDependency($item, $parent)
    {
        $itemKey = $this->getIndex()->search($item);
        if (false === $itemKey)
        {
            throw new InvalidItemException('Child item does not exist in storage');
        }
        
        $parentKey = $this->getIndex()->search($parent);
        if (false === $parentKey)
        {
            throw new InvalidItemException('Parent item does not exist in storage');
        }
        
        if (!isset($this->dependencies[$itemKey]))
        {
            $this->dependencies[$itemKey] = array();
        }
        
        $this->dependencies[$itemKey][] = $parentKey;
    }
    
    /**
     * Remove an item dependency
     * @param mixed $item
     * @param mixed $parent
     * @return void
     */
    public function removeDependency($item, $parent)
    {
        $itemKey = $this->getIndex()->search($item);
        if (false === $itemKey)
        {
            throw new InvalidItemException('Child item does not exist in storage');
        }
        
        $parentKey = $this->getIndex()->search($parent);
        if (false === $parentKey)
        {
            throw new InvalidItemException('Parent item does not exist in storage');
        }
        
        $index = array_search($parentKey, $this->dependencies[$itemKey]);
        if (false !== $index)
        {
            unset($this->dependencies[$itemKey][$index]);
        }
    }
    
    /**
     * Get dependencies of an item
     * @param mixed $item
     * @return array
     */
    public function getItemDependencies($item)
    {
        $itemKey = $this->getIndex()->search($item);
        if (false === $itemKey)
        {
            throw new InvalidItemException('Child item does not exist in storage');
        }
        
        $out = array();
        
        if (isset($this->dependencies[$itemKey]))
        {
            $items = &$this->getIndex()->getItems();
            foreach($this->dependencies[$itemKey] as $parentKey)
            {
                if (isset($items[$parentKey]))
                {
                    $out[] = $items[$parentKey];
                }
            }
        }
        
        return $out;
    }
    
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
     * 
     * All current dependencies will be cleared
     * 
     * @param Restack\Index $index
     * @return void
     */
    public function setIndex($index)
    {
        $this->clear();
        $this->index = $index;
    }
}