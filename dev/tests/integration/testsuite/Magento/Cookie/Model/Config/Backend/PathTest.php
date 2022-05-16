<?php
/**
 * Integration test for Magento\Cookie\Model\Config\Backend\Path
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cookie\Model\Config\Backend;

use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    /**
     * Method is not publicly accessible, so it must be called through parent
     *
     */
    public function testBeforeSaveException()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Invalid cookie path');

        $invalidPath = 'invalid path';
        $objectManager = Bootstrap::getObjectManager();
        /** @var Lifetime $model */
        $model = $objectManager->create(Path::class);
        $model->setValue($invalidPath);
        $model->save();
    }
}
