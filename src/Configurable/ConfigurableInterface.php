<?php

namespace VS\General\Configurable;

/**
 * Interface ConfigurableInterface
 * @package VS\General\Configurable
 * @author Varazdat Stepanyan
 */
interface ConfigurableInterface
{
    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

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