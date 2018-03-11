<?php

namespace VS\General\Configurable;

/**
 * Class ConfigurableConstants
 * @package VS\General\Configurable
 * @author Varazdat Stepanyan
 */
class ConfigurableConstants
{
    const INVALID_KEY_CODE = 1;

    const INVALID_KEY_MESSAGE = 'Configuration key [%s] not found.';

    const DEFAULT_LANG = 'en';

    protected const MESSAGES = [
        self::DEFAULT_LANG => [
            self::INVALID_KEY_CODE => self::INVALID_KEY_MESSAGE
        ]
    ];

    /**
     * @var array $messages
     */
    protected static $messages = [];

    /**
     * @param string $key
     * @param string $lang
     * @return string
     */
    public static function getMessage(string $key, string $lang = self::DEFAULT_LANG): string
    {
        $message = self::$messages[$lang][$key] ?? self::MESSAGES[$lang][$key] ?? false;
        if (!$message) {
            throw new \InvalidArgumentException(sprintf(
                'Configurable message not found in %s',
                __CLASS__
            ));
        }

        return $message;
    }

    /**
     * @param array $messages
     */
    public static function setMessages(array $messages): void
    {
        self::$messages = $messages;
    }
}