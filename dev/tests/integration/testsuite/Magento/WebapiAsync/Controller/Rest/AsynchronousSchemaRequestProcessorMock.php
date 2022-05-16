<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\WebapiAsync\Controller\Rest;

use Magento\Framework\Webapi\Rest\Request;

class AsynchronousSchemaRequestProcessorMock extends AsynchronousSchemaRequestProcessor
{
    /**
     * {@inheritdoc}
     */
    public function canProcess(Request $request)
    {
        return true;
    }
}
