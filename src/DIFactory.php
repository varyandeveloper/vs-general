<?php

namespace VS\General;

use VS\General\{
    Singleton\SingletonInterface, Exceptions\ClassNotFoundException
};

/**
 * Class DIFactory
 * @package VS\General
 * @author Varazdat Stepanyan
 */
class DIFactory
{
    /**
     * @param string $className
     * @param array ...$args
     * @return object
     * @throws ClassNotFoundException
     * @throws \ReflectionException
     */
    public static function injectClass(string $className, ...$args): object
    {
        try {
            $reflectionClass = new \ReflectionClass($className);

            if ($reflectionClass->isInterface() && in_array(SingletonInterface::class, $reflectionClass->getInterfaceNames(), true)) {
                $reflectionClass = self::resolveClass($reflectionClass);
                /**
                 * @var SingletonInterface $className
                 */
                $className = $reflectionClass->getName();
                $params = self::injectMethodParams($reflectionClass->getConstructor(), $args);
                return $className::getInstance(...$params);
            }

            $method = $reflectionClass->getConstructor();
            if (!$method || (null !== $method && $method->isPrivate())) {
                $compareArray = [];
                $interfaces = $reflectionClass->getInterfaces();
                if (count($interfaces)) {
                    $keys = array_keys($interfaces);
                    $compareArray = is_numeric($keys) ? $interfaces : array_column($interfaces, 'name');
                }
                if (in_array(SingletonInterface::class, $compareArray, true)) {
                    /**
                     * @var SingletonInterface $className
                     */
                    $params = self::injectMethodParams($method, $args);
                    return $className::getInstance(...$params);
                }
                $reflectionClass = self::resolveClass($reflectionClass);
                return $reflectionClass->newInstanceWithoutConstructor();
            }
            $params = self::injectMethodParams($method, $args);

            return self::resolveClass($reflectionClass)->newInstance(...$params);

        } catch (\ReflectionException $exception) {
            throw $exception;
        }
    }

    /**
     * @param string|object $class
     * @param string $methodName
     * @param array ...$args
     * @return mixed
     * @throws ClassNotFoundException
     * @throws \ReflectionException
     */
    public static function injectMethod($class, string $methodName, ...$args)
    {
        try {

            if (is_string($class)) {
                $reflectionClass = new \ReflectionClass($class);
                $className = self::resolveClass($reflectionClass)->getName();
                $object = self::injectClass($className);
            } elseif (is_object($class)) {
                $className = $class;
                $object = $class;
            } else {
                throw new \InvalidArgumentException("Parameter class should be either string or object.");
            }

            $method = new \ReflectionMethod($className, $methodName);
            $params = self::injectMethodParams($method, $args);

            return $method->invoke($object, ...$params);

        } catch (\ReflectionException $exception) {
            // TODO::do something with exception
            throw $exception;
        }
    }

    /**
     * @param \ReflectionFunctionAbstract $method
     * @param array $sentParams
     * @return array
     * @throws ClassNotFoundException
     * @throws \ReflectionException
     */
    private static function injectMethodParams(\ReflectionFunctionAbstract $method, array $sentParams): array
    {
        $params = $method->getParameters();
        $injectedParams = [];

        $i = 0;
        foreach ($params as $param) {
            if ($param->isOptional() && count($sentParams) < count($params)) {
                continue;
            }

            $class = $param->getClass();

            if (isset($sentParams[$i])) {
                if (is_object($param) && count($params) === 1) {
                    $injectedParams = $sentParams;
                } else {
                    $injectedParams[] = $sentParams[$i];
                    $i++;
                }
            } elseif ($class) {
                $class = self::resolveClass($class);
                $injectedParams[] = self::injectClass($class->getName());
            }
        }

        return $injectedParams;
    }

    /**
     * @param callable|\Closure|string $functionName
     * @param array ...$args
     * @return mixed
     * @throws ClassNotFoundException
     * @throws \ReflectionException
     */
    public static function injectFunction($functionName, ...$args)
    {
        try {
            $function = new \ReflectionFunction($functionName);
            $params = self::injectMethodParams($function, $args);

            return count($params) ? $function->invokeArgs($params) : $function->invoke();

        } catch (\ReflectionException $exception) {
            throw $exception;
        }
    }

    /**
     * @param \ReflectionClass $class
     * @return \ReflectionClass
     * @throws ClassNotFoundException
     * @throws \ReflectionException
     */
    private static function resolveClass(\ReflectionClass $class): \ReflectionClass
    {
        $className = $class->getName();

        if ($class->isInterface()) {
            $className = str_replace(['\\Interfaces', 'Interface'], '', $className);
            if (!class_exists($className)) {
                throw new ClassNotFoundException(sprintf(
                    'Class %s not found',
                    $className
                ));
            }
            $class = new \ReflectionClass($className);
        } else {
            return $class;
        }

        if (!class_exists($className)) {
            throw new ClassNotFoundException(sprintf(
                'Class %s not found',
                $className
            ));
        }

        return $class;
    }
}