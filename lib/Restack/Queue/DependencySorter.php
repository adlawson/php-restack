<?php

namespace Restack\Queue;

use Restack\Queue\DependencyIndex;
use \Restack\Exception\CircularDependencyException;
use \Restack\Exception\UnmetDependencyException;
use \Restack\Exception\InvalidItemException;

class DependencySorter
{
    public static function preSort( DependencyIndex $index )
    {
        $dependencies = array();
        
        foreach( $index->getChildren() as $children )
        {
            $dependencies = array_merge( $dependencies, $children );
        }
        
        if( count( array_diff( array_unique( $dependencies ), $index->getMembers() ) ) )
        {
            throw new UnmetDependencyException('Required value not found in index');
        }
    }
    
    public static function sort( DependencyIndex $index )
    {
        self::preSort( $index );
        
        $tempIndex = array();
        
        foreach( $index->getMembers() as $member )
        {
            $search = array_search( $member, $tempIndex );
            
            if( $children = $index->getChildrenOf( $member ) )
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

    public static function postSort( DependencyIndex $index )
    {
        foreach( $index->getChildren() as $member => $children )
        {
            if( array_search( $member, $index->getMembers() ) <= max( array_keys( array_intersect( $index->getMembers(), $children ) ) ) )
            {
                $index->setState( DependencyIndex::STATE_CORRUPT );
                throw new CircularDependencyException('Index corrupted');
            }
        }
    }
}