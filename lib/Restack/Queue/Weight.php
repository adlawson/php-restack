<?php

namespace Restack\Queue;

/**
 * Weight ordered queue.
 * Items added to the queue are accessed in queue fashion,
 * meaning; first in, first out.
 * 
 * Items are ordered by weight, so an item with a high
 * weight will be placed nearer the bottom of the queue.
 * 
 * @category  Restack
 * @package   Restack\Queue
 */
class Weight extends Priority
{
    /**
     * Get the weight of an item
     * @param mixed $item
     * @return integer
     */
    public function getOrder($item)
    {
        return PHP_INT_MAX - parent::getOrder($item);
    }
    
    /**
     * Set the weight of an item
     * @param mixed $item
     * @param integer $order
     * @throws Restack\Exception\InvalidItemException
     * @return Restack\Queue\Weight
     */
    public function setOrder($item, $order)
    {
        return parent::setOrder($item, PHP_INT_MAX - (int) $order);
    }
}