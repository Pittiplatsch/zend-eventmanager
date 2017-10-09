<?php

namespace ZendBench\EventManager;

use Interop\Container\ContainerInterface;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManager;

/**
 * @BeforeMethods({"setUp"})
 */
class MultipleEventListenerDefinitionBench
{
    use BenchTrait;

    private $eventsToTrigger;

    private $container;

    public function setUp()
    {
        $this->events = new EventManager();

        $this->eventsToTrigger = array_filter($this->getEventList(), function ($value) {
            return ($value !== '*');
        });

        $this->container = new Container(['listener' => new Listener()]);
    }

    /**
     * Attach and trigger the event list
     *
     * @Revs(1000)
     * @Iterations(20)
     */
    public function benchTrigger()
    {
        foreach ($this->eventsToTrigger as $event) {
            $this->events->attachDefinition($event, Listener::class, 'listen', $this->container);
            $this->events->trigger($event);
        }
    }
}

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private $instances = [];

    public function __construct(array $instances)
    {
        foreach ($instances as $instance) {
            $this->instances[get_class($instance)] = $instance;
        }
    }

    public function get($id)
    {
        return $this->instances[$id];
    }

    public function has($id)
    {
        return isset($this->instances[$id]);
    }
}

class Listener
{
    public function listen(EventInterface $event)
    {
    }
}
