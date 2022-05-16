<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * HTTP response implementation that is used instead core one for testing
 */

namespace Magento\TestFramework;

use Magento\Framework\App\Response\Http;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Response extends Http
{
    public function sendResponse()
    {
    }
}
