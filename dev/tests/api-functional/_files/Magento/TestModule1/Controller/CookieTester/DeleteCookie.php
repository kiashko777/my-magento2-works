<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule1\Controller\CookieTester;

use Magento\Framework\App\ResponseInterface;
use Magento\TestModule1\Controller\CookieTester;

/**
 * Controller to test deletion of a cookie
 */
class DeleteCookie extends CookieTester
{
    /**
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $cookieName = $this->request->getParam('cookie_name');
        $this->getCookieManager()->deleteCookie($cookieName);
        return $this->_response;
    }
}
