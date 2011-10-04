<?php

namespace Restack\Test\Queue;

use \Restack\Queue\DependencyIndex;

class DependencyIndexTest extends \PHPUnit_Framework_TestCase
{
    private $index;
    
    public function setUp()
    {
        $this->index = new DependencyIndex;
    }
    
    public function testInsert()
    {
        $this->assertEquals( DependencyIndex::STATE_SORTED, $this->index->getState() );
        
        $this->index->insert( 'a' );
        $this->index->insert( 'b' );
        $this->index->insert( 'c' );
        
        $this->assertEquals( DependencyIndex::STATE_UNSORTED, $this->index->getState() );
        
        $this->index->sort();
        
        $this->assertEquals( DependencyIndex::STATE_SORTED, $this->index->getState() );
        $this->assertEquals( 3, count( $this->index->sort() ) );
    }
    
    public function testBasicRetrieval()
    {
        $this->index->insert( 'a' );
        $this->index->insert( 'b' );
        $this->index->insert( 'c' );
        
        $this->index->sort();
        
        $this->assertEquals( array( 'a', 'b', 'c' ), $this->index->sort() );
    }
    
    public function testDependencySetInNaturalOrder()
    {
        $this->index->insert( 'a' );
        $this->index->insert( 'b' );
        $this->index->insert( 'c' );
        $this->index->addDependency( 'c', 'a' );
        
        $this->assertEquals( array( 'a', 'b', 'c' ), $this->index->sort() );
    }
    
    public function testDependencySetOutOfNaturalOrder()
    {
        $this->index->insert( 'a' );
        $this->index->insert( 'b' );
        $this->index->insert( 'c' );
        $this->index->addDependency( 'a', 'b' );
        
        $this->assertEquals( array( 'b', 'a', 'c' ), $this->index->sort() );
    }
    
    public function testDependencyNesting()
    {
        $this->index->insert( 'a' );
        $this->index->insert( 'b' );
        $this->index->insert( 'c' );
        $this->index->addDependency( 'a', 'b' );
        $this->index->addDependency( 'b', 'c' );
        
        $this->assertEquals( array( 'c', 'b', 'a' ), $this->index->sort() );
    }
    
    public function testInvalidChildError()
    {
        $this->index->insert( 'a' );
        $this->index->insert( 'b' );
        $this->index->insert( 'c' );
        $this->index->addDependency( 'a', 'b' );
        $this->index->addDependency( 'b', 'c' );
        $this->index->addDependency( 'c', 'd' );

        $this->setExpectedException( 'Restack\Exception\UnmetDependencyException' );
        
        $this->index->sort();
    }
    
    public function testDependencyRecuriveNestingError()
    {
        $this->index->insert( 'a' );
        $this->index->insert( 'b' );
        $this->index->insert( 'c' );
        $this->index->addDependency( 'a', 'b' );
        $this->index->addDependency( 'b', 'c' );
        $this->index->addDependency( 'c', 'a' );
        
        $this->setExpectedException( 'Restack\Exception\CircularDependencyException' );
        
        $this->index->sort();
    }
    
    public function testDependencyDeepNesting()
    {
        $this->index->insert( 'a' );
        $this->index->insert( 'b' );
        $this->index->insert( 'c' );
        $this->index->insert( 'd' );
        $this->index->insert( 'e' );
        $this->index->insert( 'f' );
        $this->index->insert( 'g' );
        $this->index->insert( 'h' );
        
        $this->index->addDependency( 'a', 'b' );
        $this->index->addDependency( 'b', 'c' );
        $this->index->addDependency( 'c', 'h' );
        $this->index->addDependency( 'h', 'g' );
        $this->index->addDependency( 'g', 'e' );
        
        $this->assertEquals( array( 'e', 'g', 'h', 'c', 'b', 'a', 'd', 'f' ), $this->index->sort() );
    }
}