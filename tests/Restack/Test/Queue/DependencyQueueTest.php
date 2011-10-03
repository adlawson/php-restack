<?php

namespace Restack\Test\Queue;

use Restack\Queue\DependencyQueue;

class DependencyQueueTest extends \PHPUnit_Framework_TestCase
{
    private $queue;
    
    const VAL1 = '1';
    const VAL2 = '2';
    const VAL3 = '3';
    
    public function setUp()
    {
        $this->queue = new DependencyQueue;
    }
    
    public function testInsert()
    {
        $this->queue[ 'a' ] = self::VAL1;
        $this->queue[ 'b' ] = self::VAL2;
        $this->queue[ 'c' ] = self::VAL3;
        
        $this->assertEquals( 3, count( $this->queue->toArray() ) );
    }
    
    public function testBasicRetrieval()
    {
        $this->queue[ 'a' ] = self::VAL1;
        $this->queue[ 'b' ] = self::VAL2;
        $this->queue[ 'c' ] = self::VAL3;
        
        $this->assertEquals( array( 'a', 'b', 'c' ), array_keys( $this->queue->toArray() ) );
    }
    
    public function testDependencySetInNaturalOrder()
    {
        $this->queue[ 'a' ] = self::VAL1;
        $this->queue[ 'b' ] = self::VAL2;
        $this->queue[ 'c' ] = self::VAL3;
        $this->queue->dependency( 'c', 'a' );
        
        $this->assertEquals( array( 'a', 'b', 'c' ), array_keys( $this->queue->toArray() ) );
    }
    
    public function testDependencySetOutOfNaturalOrder()
    {
        $this->queue[ 'a' ] = self::VAL1;
        $this->queue[ 'b' ] = self::VAL2;
        $this->queue[ 'c' ] = self::VAL3;
        $this->queue->dependency( 'a', 'b' );
        
        $this->assertEquals( array( 'b', 'a', 'c' ), array_keys( $this->queue->toArray() ) );
    }
    
    public function testDependencyNesting()
    {
        $this->queue[ 'a' ] = self::VAL1;
        $this->queue[ 'b' ] = self::VAL2;
        $this->queue[ 'c' ] = self::VAL3;
        $this->queue->dependency( 'a', 'b' );
        $this->queue->dependency( 'b', 'c' );
        
        $this->assertEquals( array( 'c', 'b', 'a' ), array_keys( $this->queue->toArray() ) );
    }
    
    public function testDependencyDeepNesting()
    {
        $this->queue[ 'a' ] = self::VAL1;
        $this->queue[ 'b' ] = self::VAL2;
        $this->queue[ 'c' ] = self::VAL3;
        $this->queue[ 'd' ] = self::VAL1;
        $this->queue[ 'e' ] = self::VAL2;
        $this->queue[ 'f' ] = self::VAL3;
        $this->queue[ 'g' ] = self::VAL1;
        $this->queue[ 'h' ] = self::VAL2;
        
        $this->queue->dependency( 'a', 'b' );
        $this->queue->dependency( 'b', 'c' );
        $this->queue->dependency( 'c', 'h' );
        $this->queue->dependency( 'h', 'g' );
        $this->queue->dependency( 'g', 'e' );
        
        $this->assertEquals( array( 'e', 'g', 'h', 'c', 'b', 'a', 'd', 'f' ), array_keys( $this->queue->toArray() ) );
    }
}