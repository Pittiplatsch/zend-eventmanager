<?php

namespace ZendBench\EventManager;

use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use Zend\EventManager\SharedEventManager;
use Zend\EventManager\EventManager;

/**
 * @BeforeMethods({"setUp"})
 */
class MultipleEventMultipleSharedListenerBench
{
    use BenchTrait;

    private $sharedEvents;

    private $events;

    private $eventsToTrigger;

    public function setUp()
    {
        $identifiers = $this->getIdentifierList();
        $this->sharedEvents = new SharedEventManager();
        foreach ($this->getIdentifierList() as $identifier) {
            foreach ($this->getEventList() as $event) {
                $this->sharedEvents->attach($identifier, $event, $this->generateCallback());
            }
        }
        $this->events = new EventManager($this->sharedEvents, $identifiers);

        $this->eventsToTrigger = array_filter($this->getEventList(), function ($value) {
            return ($value !== '*');
        });
    }

    /**
     * Trigger the event list
     *
     * @Revs(1000)
     * @Iterations(20)
     */
    public function benchTrigger()
    {
        foreach ($this->eventsToTrigger as $event) {
            $this->events->trigger($event);
        }
    }
}