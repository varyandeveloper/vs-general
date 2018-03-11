<?php

namespace VS\General\Configurable;

/**
 * Trait ConfigurableTrait
 * @package VS\General\Configurable
 * @author Varazdat Stepanyan
 */
trait ConfigurableTrait
{
    /**
     * @var array $config
     */
    protected $config = [];

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return !empty($this->config[$key]);
    }

    /**
     * @param string|null $key
     * @return array|mixed
     */
    public function getConfig(string $key = null)
    {
        if(null !== $key) {
            if(!isset($this->config[$key])) {
                throw new \InvalidArgumentException(sprintf(
                    'Configuration key []'
                ));
            }
            return $this->config[$key];
        }
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}