<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ImportExport\Controller\Adminhtml\Import;

use Magento\Framework\HTTP\Adapter\FileTransferFactory;
use Magento\Framework\Validator\NotEmpty;

class HttpFactoryMock extends FileTransferFactory
{
    public function create(array $options = [])
    {
        return new NotEmpty($options);
    }
}
