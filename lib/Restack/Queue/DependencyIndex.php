<?php

namespace Restack\Queue;

use \Restack\Exception\CircularDependencyException;
use \Restack\Queue\Index;

/**
 * Dependency aware Index
 */
class DependencyIndex extends Index
{
    /** @var array A list of member dependencies */
    private $dependencies = array();
    
    /**
     * Sort the index based on a sorting algorithm and return the result
     * 
     * Re-order the index in a way that prioritises dependencies first
     * This method is optimised via a result cache
     * 
     * @throws CircularDependencyException
     * @return array
     */
    public function sort()
    {
        switch( $this->getState() )
        {                
            case Index::STATE_UNSORTED:
                DependencySorter::sort( $this );
                
            case Index::STATE_SORTED:
                return $this->getMembers();
                
            default:
                $this->setState( Index::STATE_CORRUPT );
                throw new CircularDependencyException('Index corrupted');
        }
    }
    
    /**
     * Create a member dependency
     * 
     * Add a parent/child dependency mapping for use by the sorting algorithm
     * 
     * @param string $parent
     * @param string $child
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
     * Remove a member dependency
     * 
     * Remove a parent/child dependency mapping for use by the sorting algorithm
     * 
     * @param string $parent
     * @param string $child 
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
     * 
     * Returns an associative array of the parent/child mapping for a single member
     * 
     * @param string $member
     * @return array|null 
     */
    public function getDependenciesOf( $member )
    {
        return isset( $this->dependencies[ $member ] ) ? $this->dependencies[ $member ] : null;
    }
    
    /**
     * Retrieve an array containing all the dependency mappings
     * 
     * Returns an associative array of the parent/child mappings defined
     * 
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }
}