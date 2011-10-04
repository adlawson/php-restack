<?php

namespace Restack;

interface Queue
{
    public function count();
    public function push( $value );
    public function unshift( $value );
    public function shift();
    public function pop();
}

interface AdvancedQueue extends Queue
{
    public function clear();
    public function filter( $callback );
    public function map( $callback, $userdata );
    public function remove( $value );
    public function replace( $value, $value2 );
    public function reverse();
    public function search( $value );
    public function values();
    public function walk( $callback, $userdata );
}

interface SuperQueue extends AdvancedQueue
{
    public function diff( array $array1 );
    public function insertBefore( $value, $value2 );
    public function insertAfter( $value, $value2 );
    public function intersect( array $array1 );
    public function merge( array $array1 );
    public function removeRandom();
    public function shuffle();
    public function makeUnique();
    
}

interface DoublyLinkedList extends Queue, Iterator, ArrayAccess
{
    public function getIteratorMode();
    public function setIteratorMode( $mode );
    public function isEmpty();
}

interface Iterator extends \Iterator, \Transversable, \Countable
{
    public function rewind();
    public function current();
    public function key();
    public function next();
    public function valid();
    public function count();
    public function bottom();
    public function top();
}

interface ArrayAccess extends \ArrayAccess
{
    public function offsetExists( $index );
    public function offsetGet( $index );
    public function offsetSet( $index, $value );
    public function offsetUnset( $index );
}

interface IteratorAggregate extends \IteratorAggregate, \Transversable
{
    public function getIterator();
    public function setIterator();
}