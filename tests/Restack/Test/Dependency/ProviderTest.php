<?php

namespace Restack\Test\Dependency;

use Restack\Index;
use Restack\Queue\Priority;
use Restack\Dependency\Provider;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    private $provider;
    
    public function setUp()
    {
        $this->provider = new Provider(new Priority);
    }
    
    public function testInsert()
    {
        $this->assertEquals( Index::STATE_SORTED, $this->provider->getIndex()->getState() );
        
        $this->provider->getIndex()->insert( 'a' );
        $this->provider->getIndex()->insert( 'b' );
        $this->provider->getIndex()->insert( 'c' );
        
        $this->assertEquals( Index::STATE_UNSORTED, $this->provider->getIndex()->getState() );
        
        $this->provider->sort();
        
        $this->assertEquals( Index::STATE_SORTED, $this->provider->getIndex()->getState() );
        $this->assertEquals( 3, count( $this->provider->sort() ) );
    }
    
    public function testBasicRetrieval()
    {
        $this->provider->getIndex()->insert( 'a' );
        $this->provider->getIndex()->insert( 'b' );
        $this->provider->getIndex()->insert( 'c' );
        
        $this->provider->sort();
        
        $this->assertEquals( array( 'a', 'b', 'c' ), $this->provider->sort() );
    }
    
    public function testDependencySetInNaturalOrder()
    {
        $this->provider->getIndex()->insert( 'a' );
        $this->provider->getIndex()->insert( 'b' );
        $this->provider->getIndex()->insert( 'c' );
        $this->provider->addDependency( 'c', 'a' );
        
        $this->assertEquals( array( 'a', 'b', 'c' ), $this->provider->sort() );
    }
    
    public function testDependencySetOutOfNaturalOrder()
    {
        $this->provider->getIndex()->insert( 'a' );
        $this->provider->getIndex()->insert( 'b' );
        $this->provider->getIndex()->insert( 'c' );
        $this->provider->addDependency( 'a', 'b' );
        
        $this->assertEquals( array( 'b', 'a', 'c' ), $this->provider->sort() );
    }
    
    public function testDependencyNesting()
    {
        $this->provider->getIndex()->insert( 'a' );
        $this->provider->getIndex()->insert( 'b' );
        $this->provider->getIndex()->insert( 'c' );
        $this->provider->addDependency( 'a', 'b' );
        $this->provider->addDependency( 'b', 'c' );
        
        $this->assertEquals( array( 'c', 'b', 'a' ), $this->provider->sort() );
    }
    
    public function testInvalidChildError()
    {
        $this->provider->getIndex()->insert( 'a' );
        $this->provider->getIndex()->insert( 'b' );
        $this->provider->getIndex()->insert( 'c' );
        $this->provider->addDependency( 'a', 'b' );
        $this->provider->addDependency( 'b', 'c' );
        $this->provider->addDependency( 'c', 'd' );

        $this->setExpectedException( 'Restack\Exception\UnmetDependencyException' );
        
        $this->provider->sort();
    }
    
    public function testDependencyRecuriveNestingError()
    {
        $this->provider->getIndex()->insert( 'a' );
        $this->provider->getIndex()->insert( 'b' );
        $this->provider->getIndex()->insert( 'c' );
        $this->provider->addDependency( 'a', 'b' );
        $this->provider->addDependency( 'b', 'c' );
        $this->provider->addDependency( 'c', 'a' );
        
        $this->setExpectedException( 'Restack\Exception\CircularDependencyException' );
        
        $this->provider->sort();
    }
    
    public function testDependencyDeepNesting()
    {
        $this->provider->getIndex()->insert( 'a' );
        $this->provider->getIndex()->insert( 'b' );
        $this->provider->getIndex()->insert( 'c' );
        $this->provider->getIndex()->insert( 'd' );
        $this->provider->getIndex()->insert( 'e' );
        $this->provider->getIndex()->insert( 'f' );
        $this->provider->getIndex()->insert( 'g' );
        $this->provider->getIndex()->insert( 'h' );
        
        $this->provider->addDependency( 'a', 'b' );
        $this->provider->addDependency( 'b', 'c' );
        $this->provider->addDependency( 'c', 'h' );
        $this->provider->addDependency( 'h', 'g' );
        $this->provider->addDependency( 'g', 'e' );
        
        $this->assertEquals( array( 'e', 'g', 'h', 'c', 'b', 'a', 'd', 'f' ), $this->provider->sort() );
    }
}