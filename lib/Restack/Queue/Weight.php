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
     * Add an element to the queue
     * @param mixed $item
     * @param integer $weight
     * @return void
     */
    public function insert($item, $weight = self::DEFAULT_ORDER)
    {
        parent::insert($item, $weight);
    }
    
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
     * Set the weight of an existing item
     * @param mixed $item
     * @param integer $weight
     * @throws Restack\Exception\InvalidItemException
     * @return Restack\Queue\Weight
     */
    public function setOrder($item, $weight)
    {
        return parent::setOrder($item, PHP_INT_MAX - (int) $weight);
    }
}