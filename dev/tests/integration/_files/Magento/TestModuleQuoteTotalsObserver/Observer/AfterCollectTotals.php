<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TestModuleQuoteTotalsObserver\Observer;

use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\TestModuleQuoteTotalsObserver\Model\Config;

class AfterCollectTotals implements ObserverInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Config
     */
    private $config;

    /**
     * AfterCollectTotals constructor.
     * @param Session $messageManager
     * @param Config $config
     */
    public function __construct(
        Session                     $messageManager,
        Config $config
    )
    {
        $this->config = $config;
        $this->session = $messageManager;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $observer->getEvent();
        if ($this->config->isActive()) {
            $this->session->getQuote();
        }
    }
}
