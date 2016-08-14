<?php
namespace josephlavin\localEventing;

use ReflectionMethod;

trait LocalEventing
{
    /**
     * The start of a magic method name.  Start with __ to follow php magic method convention.
     * @var string
     */
    protected $localEventingMethodStart = '__onLocalEvent_';

    /**
     * If another trait wants to rely on eventing from this trait it can make this method
     * an abstract method.  If this method is then not implemented we can assume that
     * this trait was not included and a run time error will occur.  The name
     * should be a reminder to the developer to include this trait.
     *
     * @return bool
     */
    protected function _require_trait_LocalEventing()
    {
        return true;
    }

    /**
     * Execute all magic methods for this event
     *
     * Method Naming Syntax: protected function __on[Event][Action]() {};
     *
     * @param $name
     * @return array
     * @throws \Exception
     */
    private function fireLocalEvent($name)
    {
        // Put any additional arguments into an array
        $arguments = func_get_args();
        array_shift($arguments);

        // Figure out the method name to match
        $nameStart = $this->getMethodStart($name);

        return collect($this->getPrivateProtectedMethods())->filter(
            function (ReflectionMethod $method) use ($nameStart) {
                $methodName = $method->getName();
                return substr($methodName, 0, strlen($nameStart)) == $nameStart;
            }
        )->map(
            function (ReflectionMethod $method) use ($arguments) {
                return $this->executeLocalEventMethod($method, $arguments);
            }
        );
    }

    /**
     * @param ReflectionMethod $method
     * @param array $arguments
     * @return mixed
     */
    protected function executeLocalEventMethod(ReflectionMethod $method, $arguments = [])
    {
        // Allow execution of private/protected methods!
        $method->setAccessible(true);

        return $method->invokeArgs($this, $arguments);
    }

    /**
     * @param $eventName
     * @return string
     */
    private function getMethodStart($eventName)
    {
        return $this->localEventingMethodStart . $eventName;
    }

    /**
     * Return array of all private & protected methods.
     * @return ReflectionMethod[]
     */
    private function getPrivateProtectedMethods()
    {
        return (new \ReflectionClass($this))->getMethods(
            ReflectionMethod::IS_PRIVATE | ReflectionMethod::IS_PROTECTED
        );
    }
}