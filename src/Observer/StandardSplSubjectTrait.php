<?php

namespace VS\General\Observer;

/**
 * Trait StandardSplSubjectTrait
 * @package VS\General\Observer
 */
trait StandardSplSubjectTrait
{
    /**
     * @var \SplObserver[] $_observers
     */
    protected $_observers = [];

    /**
     * Attaches an SplObserver to
     * the ExceptionHandler to be notified
     * when an uncaught Exception is thrown.
     *
     * @param \SplObserver $observer
     * @return void
     */
    public function attach(\SplObserver $observer): void
    {
        $id = spl_object_hash($observer);
        $this->_observers[$id] = $observer;
    }

    /**
     * Detaches the SplObserver from the
     * ExceptionHandler, so it will no longer
     * be notified when an uncaught Exception is thrown.
     *
     * @param \SplObserver $observer
     * @return void
     */
    public function detach(\SplObserver $observer): void
    {
        $id = spl_object_hash($observer);
        unset($this->_observers[$id]);
    }

    /**
     * Notify all observers of the uncaught Exception
     * so they can handle it as needed.
     *
     * @return void
     */
    public function notify(): void
    {
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }
}