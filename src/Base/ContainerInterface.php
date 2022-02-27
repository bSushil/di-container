<?php
namespace bSushil\DependencyInjection\Base;

interface ContainerInterface {
    /**
     * @return static
     */
    public static function instance();

    /**
     * @param mixed $callable
     * @param array $parameters
     * 
     * @return object
     */
    public function call($callable, $parameters=[]);

    /**
     * @param mixed $class
     * @param array $parameters
     * 
     * @return object
     */
    public function make($class, $parameters=[]);
}