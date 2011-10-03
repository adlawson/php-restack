<?php

namespace Restack\Queue;

use Restack\Queue\DependencyStack;

class DependencyArray extends \ArrayObject
{
    private $stack;
    
    public function __construct( )
    {
        $this->setStack( new DependencyStack );
    }
    
    public function offsetSet( $offset, $value )
    {
        parent::offsetSet( $offset, $value );
        $this->getStack()->insert( $offset );
    }

    public function offsetUnset( $offset )
    {
        parent::offsetUnset( $offset );
        $this->getStack()->remove( $offset );
    }
    
    public function dependency( $parent, $child )
    {
        return $this->getStack()->dependency( $parent, $child );
    }
    
    public function toArray()
    {
        return array_combine( $this->getStack()->retrieve(), (array) $this );
    }
    
    public function getStack()
    {
        return $this->stack;
    }

    public function setStack( $stack )
    {
        $this->stack = $stack;
    }
}