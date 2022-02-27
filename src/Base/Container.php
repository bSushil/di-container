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

    /**
     * Set globally available instance of the container
     * @return static
     */
    public static function instance() {
        if(is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public function call($callable, $parameters = []) {
        
    }
}