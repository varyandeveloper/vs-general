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
            if ($param->isOptional()) {
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