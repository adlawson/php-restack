<?php

namespace Restack\Test\Dependency;

use \Restack\Dependency\ArrayIndex;

class ArrayIndexTest extends \PHPUnit_Framework_TestCase
{
    private $queue;
    
    const VAL1 = '1';
    const VAL2 = '2';
    const VAL3 = '3';
    
    public function setUp()
    {
        // Method under review
        $this->markTestIncomplete('ArrayIndex is currently under review');
        
        $this->queue = new ArrayIndex;
    }
    
    public function testInsert()
    {
        $this->queue[ 'a' ] = self::VAL1;
        $this->queue[ 'b' ] = self::VAL2;
        $this->queue[ 'c' ] = self::VAL3;
        
        $this->assertEquals( 3, count( $this->queue->toArray() ) );
        $this->assertEquals( array( 'a', 'b', 'c' ), array_keys( $this->queue->toArray() ) );
        $this->assertEquals( array( self::VAL1, self::VAL2, self::VAL3 ), array_values( $this->queue->toArray() ) );
    }
    
    public function testBasicRetrieval()
    {
        $this->queue[ 'a' ] = self::VAL1;
        $this->queue[ 'b' ] = self::VAL2;
        $this->queue[ 'c' ] = self::VAL3;
        
        $this->assertEquals( array( 'a', 'b', 'c' ), array_keys( $this->queue->toArray() ) );
        $this->assertEquals( array( self::VAL1, self::VAL2, self::VAL3 ), array_values( $this->queue->toArray() ) );
    }
    
    public function testDependencySetInNaturalOrder()
    {
        $this->queue[ 'a' ] = self::VAL1;
        $this->queue[ 'b' ] = self::VAL2;
        $this->queue[ 'c' ] = self::VAL3;
        $this->queue->addDependency( 'c', 'a' );
        
        $this->assertEquals( array( 'a', 'b', 'c' ), array_keys( $this->queue->toArray() ) );
        $this->assertEquals( array( self::VAL1, self::VAL2, self::VAL3 ), array_values( $this->queue->toArray() ) );
    }
    
    public function testDependencySetOutOfNaturalOrder()
    {
        $this->queue[ 'a' ] = self::VAL1;
        $this->queue[ 'b' ] = self::VAL2;
        $this->queue[ 'c' ] = self::VAL3;
        $this->queue->addDependency( 'a', 'b' );
        
        $this->assertEquals( array( 'b', 'a', 'c' ), array_keys( $this->queue->toArray() ) );
        $this->assertEquals( array( self::VAL2, self::VAL1, self::VAL3 ), array_values( $this->queue->toArray() ) );
    }
    
    public function testDependencyNesting()
    {
        $this->queue[ 'a' ] = self::VAL1;
        $this->queue[ 'b' ] = self::VAL2;
        $this->queue[ 'c' ] = self::VAL3;
        $this->queue->addDependency( 'a', 'b' );
        $this->queue->addDependency( 'b', 'c' );
        
        $this->assertEquals( array( 'c', 'b', 'a' ), array_keys( $this->queue->toArray() ) );
        $this->assertEquals( array( self::VAL3, self::VAL2, self::VAL1 ), array_values( $this->queue->toArray() ) );
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
        
        $this->queue->addDependency( 'a', 'b' );
        $this->queue->addDependency( 'b', 'c' );
        $this->queue->addDependency( 'c', 'h' );
        $this->queue->addDependency( 'h', 'g' );
        $this->queue->addDependency( 'g', 'e' );
        
        $this->assertEquals( array( 'e', 'g', 'h', 'c', 'b', 'a', 'd', 'f' ), array_keys( $this->queue->toArray() ) );
        $this->assertEquals( array(
            self::VAL2, self::VAL1, self::VAL2, self::VAL3, self::VAL2, self::VAL1, self::VAL1, self::VAL3
        ), array_values( $this->queue->toArray() ) );
    }
}