<?php

namespace Restack\Queue;

use Restack\Queue\DependencyStack;

class DependencyArray extends \ArrayObject
{
    private $stack;
    
    public function __construct( )
    {
        $this->stack = new DependencyStack;
    }
    
    public function offsetSet( $offset, $value )
    {
        $this->stack->insert( $offset );
        return parent::offsetSet( $offset, $value );
    }

    public function offsetUnset( $offset )
    {
        $this->stack->remove( $offset );
        return parent::offsetUnset( $offset );
    }
    
    public function dependency( $parent, $child )
    {
        return $this->stack->dependency( $parent, $child );
    }
    
    public function toArray()
    {
        return array_combine( $this->stack->retrieve(), (array) $this );
    }
}