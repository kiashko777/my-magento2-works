<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Event;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\EventManager;

/**
 * Observer of Magento events triggered using \Magento\TestFramework\EventManager::dispatch()
 */
class Magento implements ObserverInterface
{
    /**
     * Used when Magento framework instantiates the class on its own and passes nothing to the constructor
     *
     * @var EventManager
     */
    protected static $_defaultEventManager;

    /**
     * @var EventManager
     */
    protected $_eventManager;

    /**
     * Constructor
     *
     * @param EventManager $eventManager
     * @throws LocalizedException
     */
    public function __construct($eventManager = null)
    {
        $this->_eventManager = $eventManager ?: self::$_defaultEventManager;
        if (!$this->_eventManager instanceof EventManager) {
            throw new LocalizedException(
                __('Instance of the "Magento\TestFramework\EventManager" is expected.')
            );
        }
    }

    /**
     * Assign default event manager instance
     *
     * @param EventManager $eventManager
     */
    public static function setDefaultEventManager(EventManager $eventManager = null)
    {
        self::$_defaultEventManager = $eventManager;
    }

    /**
     * Handler for 'core_app_init_current_store_after' event, that converts it into 'initStoreAfter'
     * @param Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        $this->_eventManager->fireEvent('initStoreAfter');
    }
}
