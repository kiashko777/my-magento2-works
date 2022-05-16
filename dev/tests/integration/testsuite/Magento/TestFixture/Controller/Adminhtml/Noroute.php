<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFixture\Controller\Adminhtml;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Mock index controller class
 */
class Noroute implements ActionInterface
{
    /**
     * Dispatch request
     *
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function execute()
    {
    }

    /**
     * Get Response object
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
    }
}
