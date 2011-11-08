<?php

namespace Restack\Dependency;

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
     * Setup the dependency provider
     * @param Restack\Dependency\Trackable $index
     * @return void
     */
    public function __construct(Trackable $index)
    {
        $this->setIndex($index);
    }
    
    /**
     * Clear dependencies
     * @return void
     */
    public function clear()
    {
        $this->dependencies = array();
    }
    
    /**
     * Add an item dependency
     * @param mixed $item
     * @param mixed $parent
     * @throws Restack\Exception\InvalidItemException
     * @throws Restack\Exception\CircularDependencyException
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
        
        // Check for circular dependencies
        if (isset($this->dependencies[$parentKey][$itemKey]))
        {
            throw new CircularDependencyException('Circular dependency detected');
        }
        
        // Track item dependencies
        if (!isset($this->dependencies[$itemKey]))
        {
            $this->dependencies[$itemKey] = array();
        }
        $this->dependencies[$itemKey][$parentKey] = $parentKey;
        
        // Set the parent item position
        $parentOrder = $this->getIndex()->getOrder($parent);
        $itemOrder   = $this->getIndex()->getOrder($item);
        
        if ($parentOrder <= $itemOrder)
        {
            $this->getIndex()->setOrder($parent, $itemOrder + 1);
        }
    }
    
    /**
     * Remove an item dependency
     * @param mixed $item
     * @param mixed $parent
     * @throws Restack\Exception\InvalidItemException
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
        
        if (isset($this->dependencies[$itemKey][$parentKey]))
        {
            unset($this->dependencies[$itemKey][$parentKey]);
        }
    }
    
    /**
     * Get dependencies of an item
     * @param mixed $item
     * @throws Restack\Exception\InvalidItemException
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
     * @return Restack\Dependency\Trackable
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set the index instance
     * @param Restack\Dependency\Trackable $index
     * @return void
     */
    public function setIndex(Trackable $index)
    {
        $this->clear();
        $this->index = $index;
    }
}