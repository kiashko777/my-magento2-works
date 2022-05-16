<?php
/**
 * Integration test for Magento\Cookie\Model\Config\Backend\Lifetime
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cookie\Model\Config\Backend;

use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class LifetimeTest extends TestCase
{
    /**
     * Method is not publicly accessible, so it must be called through parent
     *
     */
    public function testBeforeSaveException()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Invalid cookie lifetime: must be numeric');

        $invalidCookieLifetime = 'invalid lifetime';
        $objectManager = Bootstrap::getObjectManager();
        /** @var Lifetime $model */
        $model = $objectManager->create(Lifetime::class);
        $model->setValue($invalidCookieLifetime);
        $model->save();
    }
}
