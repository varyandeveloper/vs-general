<?php

namespace VS\General;

/**
 * Class STR
 * @package VS\Framework\Common
 */
class STR
{
    /**
     * @param string $string
     * @param int|null $index
     * @return array[]|false|null|string[]
     */
    public static function splitByUppercase(string $string, ?int $index = null)
    {
        $parts = preg_split('/(?=[A-Z])/', $string, -1, PREG_SPLIT_NO_EMPTY);
        if (null !== $index) {
            return $parts[$index] ?? null;
        }
        return $parts;
    }

    /**
     * @param string $string
     * @param null $search
     * @param null $replace
     * @return string
     */
    public static function snake(string $string, $search = null, $replace = null)
    {
        if (null !== $search && null !== $replace) {
            $string = str_replace($search, $replace, $string);
        }
        $partials = self::splitByUppercase($string);
        return strtolower(implode('_', $partials));
    }

    /**
     * @param string $string
     * @param null $search
     * @param null $replace
     * @return mixed
     */
    public static function camel(string $string, $search = null, $replace = null)
    {
        if (null !== $search && null !== $replace) {
            $string = str_replace($search, $replace, $string);
        }
        $partials = explode('_', $string);
        return str_replace(' ', '', ucwords(implode(' ', $partials)));
    }
}