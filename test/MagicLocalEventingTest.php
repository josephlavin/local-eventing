<?php
use josephlavin\localEventing\LocalEventing;

class MagicLocalEventingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function it_executes_one_local_event_method()
    {
        $object = new TestLocalEventExecution();

        $object->fireSingle();

        $this->assertTrue($object->hasBeenExecuted);
    }

    /**
     * @test
     */
    function it_executes_multiple_local_event_methods()
    {
        $object = new TestLocalEventingMultiple();
        $object->fireMultiple();

        $this->assertEquals(3, $object->incremented);
    }

    /**
     * @test
     */
    function it_can_pass_arguments_to_local_event_methods()
    {
        $object = new TestLocalEventingArgumentPassing();

        $object->fireArgumentPasser('foo', 'bar');

        $this->assertEquals(['foo', 'bar'], $object->passedArguments);
    }
}

class TestLocalEventExecution
{
    use LocalEventing;

    public $hasBeenExecuted = false;

    public function fireSingle()
    {
        $this->fireLocalEvent('single');
    }

    protected function __onLocalEvent_single_setHasBeenExecuted()
    {
        $this->hasBeenExecuted = true;
    }
}

class TestLocalEventingMultiple
{
    public $incremented = 0;

    use LocalEventing;

    public function fireMultiple()
    {
        $this->fireLocalEvent('multiple');
    }

    protected function __onLocalEvent_multiple_incrementOnce()
    {
        $this->incremented++;
    }

    protected function __onLocalEvent_multiple_incrementTwice()
    {
        $this->incremented = $this->incremented + 2;
    }
}

class TestLocalEventingArgumentPassing
{
    use LocalEventing;

    public $passedArguments;

    protected function __onLocalEvent_argumentPasser_setPassedArguments($one, $two)
    {
        $this->passedArguments = [$one, $two,];
    }

    public function fireArgumentPasser($argument1, $argument2)
    {
        $this->fireLocalEvent('argumentPasser', $argument1, $argument2);
    }
}