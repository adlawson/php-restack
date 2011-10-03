<?php

namespace Restack\Test\Queue;

use Restack\Queue\DependencyStack;

class DependencyStackTest extends \PHPUnit_Framework_TestCase
{
    private $stack;
    
    public function setUp()
    {
        $this->stack = new DependencyStack;
    }
    
    public function testInsert()
    {
        $this->stack->insert( 'a' );
        $this->stack->insert( 'b' );
        $this->stack->insert( 'c' );
        
        $this->assertEquals( 3, count( $this->stack->retrieve() ) );
    }
    
    public function testBasicRetrieval()
    {
        $this->stack->insert( 'a' );
        $this->stack->insert( 'b' );
        $this->stack->insert( 'c' );
        
        $this->assertEquals( array( 'a', 'b', 'c' ), $this->stack->retrieve() );
    }
    
    public function testDependencySetInNaturalOrder()
    {
        $this->stack->insert( 'a' );
        $this->stack->insert( 'b' );
        $this->stack->insert( 'c' );
        $this->stack->dependency( 'c', 'a' );
        
        $this->assertEquals( array( 'a', 'b', 'c' ), $this->stack->retrieve() );
    }
    
    public function testDependencySetOutOfNaturalOrder()
    {
        $this->stack->insert( 'a' );
        $this->stack->insert( 'b' );
        $this->stack->insert( 'c' );
        $this->stack->dependency( 'a', 'b' );
        
        $this->assertEquals( array( 'b', 'a', 'c' ), $this->stack->retrieve() );
    }
    
    public function testDependencyNesting()
    {
        $this->stack->insert( 'a' );
        $this->stack->insert( 'b' );
        $this->stack->insert( 'c' );
        $this->stack->dependency( 'a', 'b' );
        $this->stack->dependency( 'b', 'c' );
        
        $this->assertEquals( array( 'c', 'b', 'a' ), $this->stack->retrieve() );
    }
    
    public function testDependencyDeepNesting()
    {
        $this->stack->insert( 'a' );
        $this->stack->insert( 'b' );
        $this->stack->insert( 'c' );
        $this->stack->insert( 'd' );
        $this->stack->insert( 'e' );
        $this->stack->insert( 'f' );
        $this->stack->insert( 'g' );
        $this->stack->insert( 'h' );
        
        $this->stack->dependency( 'a', 'b' );
        $this->stack->dependency( 'b', 'c' );
        $this->stack->dependency( 'c', 'h' );
        $this->stack->dependency( 'h', 'g' );
        $this->stack->dependency( 'g', 'e' );
        
        $this->assertEquals( array( 'e', 'g', 'h', 'c', 'b', 'a', 'd', 'f' ), $this->stack->retrieve() );
    }
}