<?php

namespace Restack;

/**
 * @category  Restack
 * @package   Restack\Storage
 */
interface Storage
{
    /**
     * Clear storage
     * @return void
     */
    public function clear();
    
    /**
     * Check if an item exists in storage
     * @param mixed $item
     * @return boolean
     */
    public function exists($item);
    
    /**
     * Insert an item into storage
     * @param mixed $item
     * @return void
     */
    public function insert($item);
    
    /**
     * Remove an item from storage
     * @param mixed $item
     * @return void
     */
    public function remove($item);
}