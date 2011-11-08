<?php

namespace Restack\Dependency;

/**
 * Dependable datastructure interface
 * 
 * @category  Restack
 * @package   Restack\Dependency
 */
interface Sortable
{
    /**
     * Search for an item in storage
     * @param mixed $item
     * @return integer|false Item key or false if not found
     */
    public function search($item);
    
    /**
     * Get the item index
     * @return array
     */
    public function getItems();
    
    /**
     * Get the priority of an item
     * @param mixed $item
     * @throws Restack\Exception\InvalidItemException
     * @return integer
     */
    public function getOrder($item);
    
    /**
     * Set the priority of an item
     * @param mixed $item
     * @param integer $order
     * @throws Restack\Exception\InvalidItemException
     * @return void
     */
    public function setOrder($item, $order);
}