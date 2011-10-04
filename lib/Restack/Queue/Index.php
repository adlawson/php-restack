<?php

namespace Restack\Queue;

abstract class Index
{
    const STATE_UNSORTED = 0;
    const STATE_SORTED   = 1;
    const STATE_CORRUPT  = 2;
    
    private $members  = array();    
    private $state    = self::STATE_SORTED;

    abstract function sort();
    
    public function insert( $value )
    {
        if( false === array_search( $value, $this->members ) )
        {
            $this->setState( self::STATE_UNSORTED );
            $this->members[] = $value;
        }
        
        else throw new InvalidItemException('Index values must be unique');
    }
    
    public function remove( $value )
    {
        if( false !== ( $search = array_search( $value, $this->members ) ) )
        {
            $this->setState( self::STATE_UNSORTED );
            unset( $this->members[ $search ] );
        }
        
        else throw new InvalidItemException('Value not found in index');
    }
    
    public function getMembers()
    {
        return $this->members;
    }

    public function setMembers( $members )
    {
        $this->members = $members;
    }
    
    public function getState()
    {
        return $this->state;
    }

    public function setState( $state )
    {
        $this->state = $state;
    }
}