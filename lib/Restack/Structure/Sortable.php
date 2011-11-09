<?php

namespace Restack\Structure;

/**
 * Sortable datastructure interface
 * 
 * @category  Restack
 * @package   Restack\Structure
 */
interface Sortable
{
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