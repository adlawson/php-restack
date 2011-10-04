<?php

namespace Restack\Test\Dependency;

use \Restack\Dependency\Index;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    private $index;
    
    public function setUp()
    {
        $this->index = new Index;
    }
    
    public function testInsert()
    {
        $this->assertEquals( Index::STATE_SORTED, $this->index->getState() );
        
        $this->index->insert( 'a' );
        $this->index->insert( 'b' );
        $this->index->insert( 'c' );
        
        $this->assertEquals( Index::STATE_UNSORTED, $this->index->getState() );
        
        $this->index->sort();
        
        $this->assertEquals( Index::STATE_SORTED, $this->index->getState() );
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