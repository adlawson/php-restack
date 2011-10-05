<?php

namespace Restack\Dependency;

/**
 * A dependency aware implementation of ArrayObject
 * 
 * @category  Restack
 * @package   Restack\Dependency
 */
class ArrayIndex extends \ArrayObject
{
    /**
     * The dependency index
     * @var Restack\Dependency\Index
     */
    private $index;
    
    /**
     * ArrayObject constructor
     * 
     * Creates a new index to manage sorting dependencies
     * 
     * @inheritDoc
     */
    public function __construct( $array = array() )
    {
        parent::__construct( $array );
        $this->setIndex( new Index );
    }
    
    /**
     * Add a parent-child dependency mapping to the index
     * @see Restack\Dependency\Index::addDependency
     * @param string $parent
     * @param string $child 
     * @return void
     */
    public function addDependency( $parent, $child )
    {
        $this->getIndex()->addDependency( $parent, $child );
    }
    
    /**
     * Remove a parent-child dependency mapping from the index
     * @see Restack\Dependency\Index::removeDependency
     * @param string $parent
     * @param string $child
     * @return void
     */
    public function removeDependency( $parent, $child )
    {
        $this->getIndex()->removeDependency( $parent, $child );
    }
    
    /**
     * Export the sorted ArrayObject
     * 
     * Sort the ArrayObject based on the defined dependencies
     * 
     * @see Restack\Dependency\Index::sort
     * @return array
     */
    public function toArray()
    {
        return array_replace( array_flip( (array) $this->getIndex()->sort() ), (array) $this );
    }
    
    /**
     * Update the index when the ArrayObject receives a new element
     * @inheritDoc
     */
    public function offsetSet( $offset, $value )
    {
        parent::offsetSet( $offset, $value );
        $this->getIndex()->insert( $offset );
    }

    /**
     * Update the index when the ArrayObject has an element removed
     * @inheritDoc
     */
    public function offsetUnset( $offset )
    {
        parent::offsetUnset( $offset );
         $this->getIndex()->remove( $offset );
    }
    
    /**
     * Retrieve the index object
     * @return Restack\Dependency\Index
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set the index object
     * 
     * Replaces the previously defined index object used to sort the ArrayObject
     * 
     * @param Restack\Dependency\Index $index 
     * @return void
     */
    public function setIndex( Index $index )
    {
        $this->index = $index;
    }
}