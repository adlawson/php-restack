<?php

namespace Restack\Queue;

/**
 * Abstract class to assist in maintaining indices
 */
abstract class Index
{
    /** Index States **/
    const STATE_UNSORTED = 0;
    const STATE_SORTED   = 1;
    const STATE_CORRUPT  = 2;
    
    /** @var array A simple list index members */
    private $members = array();
    
    /** @var int A class constant which defines the index state */
    private $state = self::STATE_SORTED;

    /**
     * Sort the index based on a sorting algorithm and return the result
     * 
     * @return array
     */
    abstract function sort();
    
    /**
     * Add a new member to the index
     * 
     * Throws an InvalidItemException if the member previously exists in the index
     * 
     * @param string $member
     * @throws InvalidItemException
     */
    public function insert( $member )
    {
        if( false === array_search( $member, $this->members ) )
        {
            $this->setState( self::STATE_UNSORTED );
            $this->members[] = $member;
        }
        
        else throw new InvalidItemException('Index values must be unique');
    }
    
    /**
     * Remove a member from the index
     * 
     * Throws an InvalidItemException if the member does not exist in the index
     * 
     * @param string $member
     * @throws InvalidItemException
     */
    public function remove( $member )
    {
        if( false !== ( $search = array_search( $member, $this->members ) ) )
        {
            $this->setState( self::STATE_UNSORTED );
            unset( $this->members[ $search ] );
        }
        
        else throw new InvalidItemException('Value not found in index');
    }
    
    /**
     * Retrieve an array containing all the index members
     * 
     * Returns a non-associative array of the index members
     * 
     * @return array
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Repopulate the list containing the index members
     * 
     * Replaces the previous index with a new one
     * 
     * @param array $members 
     */
    public function setMembers( array $members )
    {
        $this->members = $members;
    }
    
    /**
     * Get the index state
     * 
     * @return int 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set the index state
     * 
     * @param int $state 
     */
    public function setState( $state )
    {
        $this->state = $state;
    }
}