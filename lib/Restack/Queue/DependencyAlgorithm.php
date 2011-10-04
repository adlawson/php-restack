<?php

namespace Restack\Queue;

use \Restack\Queue\DependencyIndex;
use \Restack\Exception\CircularDependencyException;
use \Restack\Exception\UnmetDependencyException;

/**
 * Dependency sorting algorithm for use with the DependencyIndex
 */
class DependencyAlgorithm
{
    /**
     * Validate the index dependencies
     * 
     * Check all defined children are members of the index
     * 
     * @param DependencyIndex $index
     * @throws UnmetDependencyException
     */
    public static function pre( DependencyIndex $index )
    {
        $dependencies = array();
        
        foreach( $index->getDependencies() as $children )
        {
            $dependencies = array_merge( $dependencies, $children );
        }
        
        if( count( array_diff( array_unique( $dependencies ), $index->getItems() ) ) )
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
    public static function run( DependencyIndex $index )
    {
        $tempIndex = array();
        
        foreach( $index->getItems() as $item )
        {
            $search = array_search( $item, $tempIndex );
            
            if( $children = $index->getDependenciesOf( $item ) )
            {
                array_splice( $tempIndex, ( false !== $search ) ? $search : 0, 0, $children );
                $tempIndex = array_unique( $tempIndex );
            }
            
            if( false === $search )
            {
                $tempIndex[] = $item;
            }
        }

        $index->setState( DependencyIndex::STATE_SORTED );
        $index->setItems( array_values( $tempIndex ) );
    }

    /**
     * Validate the result of the sort
     * 
     * Check the output was not corrupted by errors such as circular dependencies
     * 
     * @param DependencyIndex $index
     * @throws CircularDependencyException
     */
    public static function post( DependencyIndex $index )
    {
        foreach( $index->getDependencies() as $item => $children )
        {
            if( array_search( $item, $index->getItems() ) <= max( array_keys( array_intersect( $index->getItems(), $children ) ) ) )
            {
                $index->setState( DependencyIndex::STATE_CORRUPT );
                throw new CircularDependencyException('Index corrupted');
            }
        }
    }
}