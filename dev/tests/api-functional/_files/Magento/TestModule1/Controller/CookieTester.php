<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule1\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;

/**
 * Controller for testing the CookieManager.
 *
 */
abstract class CookieTester implements ActionInterface
{
    /** @var PhpCookieManager */
    protected $cookieManager;

    /** @var  CookieMetadataFactory */
    protected $cookieMetadataFactory;

    /**
     * @var ResponseInterface
     */
    protected $_response;

    /**
     * @var
     */
    protected $request;

    /**
     * @param Context $context
     * @param PhpCookieManager $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        Context $context,
        PhpCookieManager                      $cookieManager,
        CookieMetadataFactory                 $cookieMetadataFactory
    )
    {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->_response = $context->getResponse();
        $this->request = $context->getRequest();
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $this->request = $request;
        $result = $this->execute();
        return $result ? $result : $this->_response;
    }

    /**
     * Retrieve cookie metadata factory
     */
    protected function getCookieMetadataFactory()
    {
        return $this->cookieMetadataFactory;
    }

    /**
     * Retrieve cookie metadata factory
     */
    protected function getCookieManager()
    {
        return $this->cookieManager;
    }
}
