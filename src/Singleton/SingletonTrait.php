<?php

namespace VS\General\Singleton;

/**
 * Trait SingletonTrait
 * @package VS\General\Singleton
 * @author Varazdat Stepanyan
 */
trait SingletonTrait
{
    /**
     * @var object $instance
     */
    private static $instance;

    /**
     * @param array ...$args
     * @return object
     */
    public static function getInstance(...$args): object
    {
        if(!self::$instance) {
            self::$instance = count($args)
                ? new self(...$args)
                : new self;
        }
        return self::$instance;
    }

    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}
}