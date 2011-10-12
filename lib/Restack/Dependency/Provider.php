<?php

namespace Restack\Dependency;

use Restack\Index;
use Restack\Exception\CircularDependencyException;

/**
 * Dependency aware index
 * 
 * @category  Restack
 * @package   Restack\Dependency
 */
class Provider
{
    /**
     * Item dependencies
     * @var array
     */
    private $dependencies = array();
    
    /**
     * The index instance
     * @var Restack\Index
     */
    private $index;
    
    /**
     * Setup the dependency provider
     * @param Restack\Index $index
     * @return void
     */
    public function __construct(Index $index)
    {
        $this->setIndex($index);
    }
    
    /**
     * Sort the index based on a sorting algorithm and return the result
     * 
     * Re-order the index in a way that prioritises dependencies first
     * This method is optimised via a result cache
     * 
     * @throws Restack\Exception\CircularDependencyException
     * @return array
     */
    public function sort()
    {
        switch( $this->getIndex()->getState() )
        {                
            case Index::STATE_UNSORTED:
                Algorithm::pre( $this );
                Algorithm::run( $this );
                Algorithm::post( $this );
                
            case Index::STATE_SORTED:
                return $this->getIndex()->getItems();
                
            default:
                $this->getIndex()->setState( Index::STATE_CORRUPT );
                throw new CircularDependencyException('Index corrupted');
        }
    }
    
    /**
     * Add an item dependency
     * @param string $parent
     * @param string $child
     * @return void
     */
    public function addDependency( $parent, $child )
    {
        if( !isset( $this->dependencies[ $parent ] ) )
        {
            $this->dependencies[ $parent ] = array();
        }
        
        $this->dependencies[ $parent ][] = $child;
        $this->dependencies[ $parent ] = array_unique( $this->dependencies[ $parent ], \SORT_STRING );
    }
    
    /**
     * Remove an item dependency
     * @param string $parent
     * @param string $child
     * @return void
     */
    public function removeDependency( $parent, $child )
    {
        if( isset( $this->dependencies[ $parent ] ) )
        {
            $search = array_search( $child, $this->dependencies[ $parent ] );
            
            if( false !== $search )
            {
                unset( $this->dependencies[ $parent ][ $search ] );
            }
        }
    }
    
    /**
     * Get all registered item dependencies
     * @return array An associative array of the parent/child mappings defined
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }
    
    /**
     * Get registered dependencies for a specific item
     * @param mixed $item
     * @return array|null An associative array of the parent/child mapping for an item
     */
    public function getItemDependencies( $item )
    {
        return isset( $this->dependencies[ $item ] ) ? $this->dependencies[ $item ] : null;
    }
    
    /**
     * Get the index instance
     * @return Restack\Index
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set the index instance
     * @param Restack\Index $index
     * @return void
     */
    public function setIndex(Index $index)
    {
        $this->index = $index;
    }


}