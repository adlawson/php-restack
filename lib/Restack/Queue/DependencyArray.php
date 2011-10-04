<?php

namespace Restack\Queue;

use Restack\Queue\DependencyIndex;

class DependencyArray extends \ArrayObject
{
    /** @var DependencyIndex */
    private $index;
    
    public function __construct( )
    {
        $this->setIndex( new DependencyIndex );
    }
    
    public function offsetSet( $offset, $value )
    {
        parent::offsetSet( $offset, $value );
        $this->getIndex()->insert( $offset );
    }

    public function offsetUnset( $offset )
    {
        parent::offsetUnset( $offset );
         $this->getIndex()->remove( $offset );
    }
    
    public function addDependency( $parent, $child )
    {
        $this->getIndex()->addDependency( $parent, $child );
    }
    
    public function removeDependency( $parent, $child )
    {
        $this->getIndex()->removeDependency( $parent, $child );
    }
    
    public function toArray()
    {
        return array_replace( array_flip( (array) $this->getIndex()->sort() ), (array) $this );
    }
    
    /** @return DependencyIndex */
    public function getIndex()
    {
        return $this->index;
    }

    public function setIndex( DependencyIndex $index )
    {
        $this->index = $index;
    }
}