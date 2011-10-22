<?php

namespace Restack\Queue;

/**
 * Queue interface.
 * Provides methods for geting and setting
 * item order
 * 
 * @category  Restack
 * @package   Restack\Queue
 */
interface Queue
{
    /**
     * Get the order of an item
     * @param mixed $item
     * @throws Restack\Exception\InvalidItemException
     * @return integer
     */
    public function getOrder($item);
    
    /**
     * Set the order of an item
     * @param mixed $item
     * @param integer $priority
     * @throws Restack\Exception\InvalidItemException
     * @return void
     */
    public function setOrder($item, $order);
}