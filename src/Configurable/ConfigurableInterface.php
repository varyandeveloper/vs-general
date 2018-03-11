<?php

namespace VS\General\Configurable;

/**
 * Interface ConfigurableInterface
 * @package VS\General\Configurable
 */
interface ConfigurableInterface
{
    /**
     * @param string|null $key
     * @return mixed
     */
    public function getConfig(string $key = null);

    /**
     * @param array $config
     * @return void
     */
    public function setConfig(array $config);
}