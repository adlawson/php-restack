<?php

namespace Restack\Queue;

use \Restack\Queue\DependencyIndex;
use \Restack\Exception\CircularDependencyException;
use \Restack\Exception\UnmetDependencyException;

/**
 * Dependency sorting algorithm for use with the DependencyIndex
 */
class DependencySorter
{
    /**
     * Validate the index dependencies
     * 
     * Check all defined children are members of the index
     * 
     * @param DependencyIndex $index
     * @throws UnmetDependencyException
     */
    public static function preSort( DependencyIndex $index )
    {
        $dependencies = array();
        
        foreach( $index->getDependencies() as $children )
        {
            $dependencies = array_merge( $dependencies, $children );
        }
        
        if( count( array_diff( array_unique( $dependencies ), $index->getMembers() ) ) )
        {
            throw new UnmetDependencyException('Required value not found in index');
        }
    }
    
    /**
     * Sort the index based on dependencies
     * 
     * Re-order the index in a way that prioritises dependencies first
     * 
     * @param DependencyIndex $index 
     */
    public static function sort( DependencyIndex $index )
    {
        self::preSort( $index );
        
        $tempIndex = array();
        
        foreach( $index->getMembers() as $member )
        {
            $search = array_search( $member, $tempIndex );
            
            if( $children = $index->getDependenciesOf( $member ) )
            {
                array_splice( $tempIndex, ( false !== $search ) ? $search : 0, 0, $children );
                $tempIndex = array_unique( $tempIndex );
            }
            
            if( false === $search )
            {
                $tempIndex[] = $member;
            }
        }

        $index->setState( DependencyIndex::STATE_SORTED );
        $index->setMembers( array_values( $tempIndex ) );
        
        self::postSort( $index );
    }

    /**
     * Validate the result of the sort
     * 
     * Check the output was not corrupted by errors such as circular dependencies
     * 
     * @param DependencyIndex $index
     * @throws CircularDependencyException
     */
    public static function postSort( DependencyIndex $index )
    {
        foreach( $index->getDependencies() as $member => $children )
        {
            if( array_search( $member, $index->getMembers() ) <= max( array_keys( array_intersect( $index->getMembers(), $children ) ) ) )
            {
                $index->setState( DependencyIndex::STATE_CORRUPT );
                throw new CircularDependencyException('Index corrupted');
            }
        }
    }
}