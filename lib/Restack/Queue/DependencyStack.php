<?php

namespace Restack\Queue;

class DependencyStack
{
    private $keys   = array();
    private $map    = array();
    
    public function retrieve()
    {
        $return = array();
        
        foreach( $this->keys as $key )
        {
            $search = array_search( $key, $return );
            
            if( isset( $this->map[ $key ] ) )
            {
                array_splice( $return, ( false !== $search ) ? $search : 0, 0, $this->map[ $key ] );
                $return = array_unique( $return );
            }
            
            if( false === $search )
            {
                $return[] = $key;
            }
        }
        
        return array_merge( $return );
    }
    
    public function dependency( $parent, $child )
    {
        if( !isset( $this->map[ $parent ] ) )
        {
            $this->map[ $parent ] = array();
        }
        
        $this->map[ $parent ][] = $child;
        $this->map[ $parent ] = array_unique( $this->map[ $parent ], \SORT_REGULAR );
    }
    
    
    public function insert( $key )
    {
        $this->keys[] = $key;
    }
    
    public function remove( $key )
    {
        if( isset( $this->keys[ $key ] ) )
        {
            unset( $this->keys[ $key ] );
        }
    }
}