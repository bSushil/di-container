<?php
namespace bSushil\DependencyInjection;

use bSushil\DependencyInjection\Base\ContainerInterface;

use \Exception;
use \ReflectionClass;
use \ReflectionException;
use \ReflectionMethod;
use \ReflectionNamedType;

class Container implements ContainerInterface {

    /**
     * Container instance
     * @var static
     */
    protected static $instance;

    /**
     * The class name with namespace
     * @var string
     */
    protected $callbackClass;

    /**
     * Whether the class has been instantiated or not
     * @var bool
     */
    protected $isClassInstantiated;

    /**
     * Name of the method of the provided class
     * @var string
     */
    protected $callbackMethod;

    /**
     * Separator for method from class
     * @var string
     */
    protected $methodSeparator = '@';

    /**
     * Namespace of class
     * @var string
     */
    public $namespace = 'App\\Controllers\\';

    final public function __construct() {}

    /**
     * Set globally available instance of the container
     * @return static
     */
    public static function instance() {
        if(is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param mixed $callable
     * @param array $parameters
     * 
     * @return object
     * 
     * @throws Exception
     * @throws ReflectionException
     */
    public function call($callable, $parameters = []) {
        $this->resolveCallback($callable);

        $methodReflection = new ReflectionMethod($this->callbackClass, $this->callbackMethod);
        $methodParams = $methodReflection->getParameters();

        $dependencies = [];

        foreach($methodParams as $param) {
            $type = $param->getType();

            if($type && $type instanceof ReflectionNamedType) {
                $name = $param->getClass()->newInstance();
                array_push($dependencies, $name);
            } else {
                $name = $param->getName();

                if(array_key_exists($name, $parameters)) {
                    array_push($dependencies, $parameters[$name]);
                } else {
                    if(!$param->isOptional()) {
                        throw new Exception('Cannot resolve parameters');
                    }
                }
            }
        }

        $initClass = $this->callbackClass;
        if(!is_object($this->callbackClass)) {
            $initClass = $this->make($this->callbackClass, $parameters);
        }

        return $methodReflection->invoke($initClass, ...$dependencies);
    }

    /**
     * @param mixed $callable
     */
    private function resolveCallback($callable) {
        if(is_string($callable)) {
            $segments = explode($this->methodSeparator, $callable);
            $this->callbackClass = $this->namespace.$segments[0];
            $this->callbackMethod = isset($segments[1])? $segments:'__invoke';
        }

        if(is_array($callable)) {
            if(is_object($callable[0])) {
                $this->callbackClass = $callable[0];
            }

            if(is_string($callable[0])) {
                $this->callbackClass = $this->namespace.$callable[0];
            }

            $this->callbackMethod = isset($callable[1])?$callable[1]:'__invoke';
        }
    }

    /**
     * @param mixed $class
     * @param array $parameters=[]
     * 
     * @return object
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function make($class, $parameters=[]) {
        $classReflection = new ReflectionClass($class);
        $constructorParams = $classReflection->getConstructor() ? $classReflection->getConstructor()->getParameters() : [];
        $dependencies = [];

        foreach($constructorParams as $param) {
            $type = $param->getType();

            if($type && $type instanceof ReflectionNamedType) {
                array_push($dependencies, $param->getClass()->newInstance());
            } else {
                $name = $param->getName();

                if(!empty($parameters) && array_key_exists($name, $parameters)) {
                    array_push($dependencies, $parameters[$name]);
                } else {
                    if(!$param->isOptional()) {
                        throw new Exception('Cannot resolve parameters');
                    }
                }
            }
        }

        return $classReflection->newInstance(...$dependencies);
    }
}