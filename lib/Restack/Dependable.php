<?php

namespace Restack;

/**
 * Provides method for dependency tracking
 * 
 * @category  Restack
 * @package   Restack\Storage
 */
interface Dependable
{
    /**
     * Add an item dependency
     * @param mixed $parent
     * @param mixed $child
     * @return void
     */
    public function addDependency($parent, $child);
}