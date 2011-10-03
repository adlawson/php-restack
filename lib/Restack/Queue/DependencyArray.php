<?php

namespace Restack\Queue;

use Restack\Queue\DependencyStack;

class DependencyArray extends \ArrayObject
{
    /** @var DependencyStack */
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
        $this->getStack()->dependency( $parent, $child );
    }
    
    public function toArray()
    {
        return array_combine( $this->getStack()->retrieve(), (array) $this );
    }
    
    /** @return DependencyStack */
    public function getStack()
    {
        return $this->stack;
    }

    public function setStack( DependencyStack $stack )
    {
        $this->stack = $stack;
    }
}