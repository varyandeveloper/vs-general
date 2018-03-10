<?php

namespace VS\General\Singleton;

/**
 * Interface SingletonInterface
 * @package VS\General\Singleton
 * @author Varazdat Stepanyan
 */
interface SingletonInterface
{
    /**
     * @return object
     */
    public static function getInstance(): object;
}