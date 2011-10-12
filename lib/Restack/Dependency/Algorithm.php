<?php

namespace Restack\Dependency;

use Restack\Exception\CircularDependencyException;
use Restack\Exception\UnmetDependencyException;
use Restack\Index;

/**
 * Dependency sorting algorithm for use
 * with the dependency index
 * 
 * @category  Restack
 * @package   Restack\Dependency
 */
class Algorithm
{
    /**
     * Validate the index dependencies
     * 
     * Check all defined children are members of the index
     * 
     * @param Restack\Dependency\Provider $provider
     * @throws Restack\Exception\UnmetDependencyException
     * @return void
     */
    public static function pre( Provider $provider )
    {
        $dependencies = array();
        
        foreach( $provider->getDependencies() as $children )
        {
            $dependencies = array_merge( $dependencies, $children );
        }
        
        if( count( array_diff( array_unique( $dependencies ), $provider->getIndex()->getItems() ) ) )
        {
            throw new UnmetDependencyException('Required value not found in index');
        }
    }
    
    /**
     * Sort the index based on dependencies
     * 
     * Re-order the index in a way that prioritises dependencies first
     * 
     * @param Restack\Dependency\Provider $provider 
     * @return void
     */
    public static function run( Provider $provider )
    {
        $tempIndex = array();
        
        foreach( $provider->getIndex()->getItems() as $item )
        {
            $search = array_search( $item, $tempIndex );
            
            $children = $provider->getItemDependencies( $item );
            if( null !== $children )
            {
                array_splice( $tempIndex, ( false !== $search ) ? $search : 0, 0, $children );
                $tempIndex = array_unique( $tempIndex );
            }
            
            if( false === $search )
            {
                $tempIndex[] = $item;
            }
        }

        $provider->getIndex()->setState( Index::STATE_SORTED );
        $provider->getIndex()->setItems( array_values( $tempIndex ) );
    }

    /**
     * Validate the result of the sort
     * 
     * Check the output was not corrupted by errors such as circular dependencies
     * 
     * @param Restack\Dependency\Provider $provider
     * @throws Restack\Exception\CircularDependencyException
     * @return void
     */
    public static function post( Provider $provider )
    {
        foreach( $provider->getDependencies() as $item => $children )
        {
            if( array_search( $item, $provider->getIndex()->getItems() ) <= max( array_keys( array_intersect( $provider->getIndex()->getItems(), $children ) ) ) )
            {
                $provider->getIndex()->setState( Index::STATE_CORRUPT );
                throw new CircularDependencyException('Index corrupted');
            }
        }
    }
}