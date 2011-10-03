<?php

namespace Restack\Queue;

use \Restack\Exception\CircularDependencyException;

use \Restack\Queue\Index;

class DependencyIndex extends Index
{
    private $children = array();
    
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
    
    public function addDependency( $parent, $child )
    {
        if( !isset( $this->children[ $parent ] ) )
        {
            $this->children[ $parent ] = array();
        }
        
        $this->children[ $parent ][] = $child;
        $this->children[ $parent ] = array_unique( $this->children[ $parent ], \SORT_STRING );
    }
    
    public function removeDependency( $parent, $child )
    {
        if( isset( $this->children[ $parent ] ) )
        {
            $search = array_search( $child, $this->children[ $parent ] );
            
            if( false !== $search )
            {
                unset( $this->children[ $parent ][ $search ] );
            }
        }
    }
    
    public function getChildrenOf( $member )
    {
        return isset( $this->children[ $member ] ) ? $this->children[ $member ] : null;
    }
    
    public function getChildren()
    {
        return $this->children;
    }
}