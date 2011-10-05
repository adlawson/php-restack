<?php

namespace Restack\Dependency;

use Restack\Exception\CircularDependencyException;

/**
 * Dependency aware index
 * 
 * @category  Restack
 * @package   Restack\Dependency
 */
class Index extends \Restack\Index
{
    /**
     * Item dependencies
     * @var array
     */
    private $dependencies = array();
    
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
        switch( $this->getState() )
        {                
            case Index::STATE_UNSORTED:
                Algorithm::pre( $this );
                Algorithm::run( $this );
                Algorithm::post( $this );
                
            case Index::STATE_SORTED:
                return $this->getItems();
                
            default:
                $this->setState( Index::STATE_CORRUPT );
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
     * Retrieve the dependency mapping for a single member
     * @param string $item
     * @return array|null An associative array of the parent/child mapping for a single member
     */
    public function getDependenciesOf( $item )
    {
        return isset( $this->dependencies[ $item ] ) ? $this->dependencies[ $item ] : null;
    }
    
    /**
     * Retrieve an array containing all the dependency mappings
     * @return array An associative array of the parent/child mappings defined
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }
}