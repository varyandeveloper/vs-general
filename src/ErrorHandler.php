<?php

namespace VS\General;

use VS\General\Observer\StandardSplSubjectTrait;

/**
 * Class ErrorHandler
 * @package VS\General
 * @author Varazdat Stepanyan
 */
class ErrorHandler implements \SplSubject
{
    use StandardSplSubjectTrait;

    /**
     * @var \Throwable $exception
     */
    protected $throwable;

    /**
     * @return \Throwable
     */
    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

    /**
     * @param \Throwable $throwable
     * @return void
     */
    public function handle(\Throwable $throwable): void
    {
        $this->throwable = $throwable;
        $this->notify();
    }
}