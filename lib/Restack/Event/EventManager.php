<?php

namespace Restack\Event;

use Restack\Queue\Priority;

class EventManager
{
    /**
     * The registered listeners
     * @var array
     */
    protected $listeners;
    
    /**
     * Attach an event listener
     * @param string|array $events
     * @param mixed $listener
     * @param integer $priority
     * @return void
     */
    public function attachListener($events, $listener, $priority = null)
    {
        foreach ((array) $events as $event) {
            if (!isset($this->listeners[$event])) {
                $this->listeners[$event] = new Priority;
            }
            
            $this->listeners[$event]->insert($listener, $priority);
        }
    }
    
    /**
     * Remove an event listener
     * @param string|array $events
     * @param mixed $listener
     * @return void
     */
    public function removeListener($events, $listener)
    {
        foreach ((array) $events as $event) {
            if (isset($this->listeners[$event])) {
                $this->listeners[$event]->remove($listener);
            }
        }
    }
    
    /**
     * Trigger registered event listeners
     * @param string $event
     * @param Restack\Event\Arguments $eventArgs
     * @return void
     */
    public function triggerListeners($event, Arguments $eventArgs = null)
    {
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listener) {
                $listener->$event($eventArgs);
            }
        }
    }
}