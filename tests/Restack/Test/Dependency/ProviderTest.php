<?php

namespace Restack\Test\Dependency;

use Restack\Queue\Priority;
use Restack\Dependency\Provider;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The index instance
     * @var Restack\Index
     */
    private $provider;
    
    /**
     * Item instance 1
     * @var stdClass
     */
    protected $obj1;
    
    /**
     * Item instance 2
     * @var stdClass
     */
    protected $obj2;
    
    /**
     * Get the dependency provider instance
     * @return Restack\Dependency\Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }
    
    /**
     * Set the dependency provider instance
     * @param Restack\Dependency\Provider $provider
     * @return void
     */
    public function setProvider(Provider $provider)
    {
        $this->provider = $provider;
    }
    
    /**
     * Setup the dependency queue
     * @return void
     */
    public function setUp()
    {
        $this->obj1 = new \stdClass;
        $this->obj2 = new \stdClass;
        
        $this->setProvider(new Provider(new Priority));
        
        $this->getProvider()->getIndex()->insert('a');
        $this->getProvider()->getIndex()->insert('b');
        $this->getProvider()->getIndex()->insert('c');
        $this->getProvider()->getIndex()->insert('d');
        $this->getProvider()->getIndex()->insert($this->obj1);
        $this->getProvider()->getIndex()->insert($this->obj2);
    }
    
    /**
     * Add item dependencies
     * @covers Restack\Dependency\Provider::addDependency()
     */
    public function testAddDependency()
    {
        $this->getProvider()->addDependency('a', 'b');
        $this->getProvider()->addDependency('a', 'c');
        $this->getProvider()->addDependency('b', 'c');
        $this->getProvider()->addDependency($this->obj1, 'd');
        $this->getProvider()->addDependency($this->obj1, $this->obj2);
        
        $this->assertSame($this->getProvider()->getItemDependencies('a'), array('b', 'c'));
        $this->assertSame($this->getProvider()->getItemDependencies('b'), array('c'));
        $this->assertSame($this->getProvider()->getItemDependencies($this->obj1), array('d', $this->obj2));
        
        $this->setExpectedException('Restack\Exception\InvalidItemException');
        $this->getProvider()->addDependency('a', 'invalid_item');
    }
    
    /**
     * Remove item dependencies
     * @covers Restack\Dependency\Provider::removeDependency()
     */
    public function testRemoveDependency()
    {
        $this->getProvider()->addDependency('a', 'b');
        $this->getProvider()->addDependency('a', 'c');
        $this->getProvider()->addDependency('a', 'd');
        $this->getProvider()->addDependency('a', $this->obj1);
        $this->getProvider()->addDependency('a', $this->obj2);
        
        $this->getProvider()->removeDependency('a', 'c');
        $this->getProvider()->removeDependency('a', $this->obj2);
        
        $this->assertSame($this->getProvider()->getItemDependencies('a'), array('b', 'd', $this->obj1));
        
        $this->setExpectedException('Restack\Exception\InvalidItemException');
        $this->getProvider()->removeDependency('a', 'invalid_item');
    }
    
    /**
     * Get item dependencies
     * @covers Restack\Dependency\Provider::getItemDependencies()
     */
    public function testGetItemDependencies()
    {
        $this->getProvider()->addDependency('a', 'b');
        $this->getProvider()->addDependency('b', 'c');
        $this->getProvider()->addDependency('a', 'd');
        $this->getProvider()->addDependency('d', $this->obj1);
        $this->getProvider()->addDependency('a', $this->obj2);
        
        $this->assertSame($this->getProvider()->getItemDependencies('a'), array('b', 'd', $this->obj2));
        $this->assertSame($this->getProvider()->getItemDependencies('b'), array('c'));
        $this->assertSame($this->getProvider()->getItemDependencies('c'), array());
        $this->assertSame($this->getProvider()->getItemDependencies('d'), array($this->obj1));
        
        $this->setExpectedException('Restack\Exception\InvalidItemException');
        $this->getProvider()->getItemDependencies('invalid_item');
    }
    
    /**
     * Clear all dependencies
     * @covers Restack\Dependency\Provider::clear()
     */
    public function testClear()
    {
        $init = $this->getProvider();
        
        $this->getProvider()->addDependency('a', 'b');
        $this->getProvider()->addDependency('b', 'c');
        $this->getProvider()->addDependency('a', 'd');
        $this->getProvider()->addDependency('d', $this->obj1);
        $this->getProvider()->addDependency('a', $this->obj2);
        
        $this->getProvider()->clear();
        
        $this->assertSame($this->getProvider(), $init);
    }
}