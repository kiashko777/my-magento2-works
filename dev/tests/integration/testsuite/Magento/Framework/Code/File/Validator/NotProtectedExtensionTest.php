<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Code\File\Validator;

use Magento\MediaStorage\Model\File\Validator\NotProtectedExtension;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class NotProtectedExtension
 */
class NotProtectedExtensionTest extends TestCase
{
    /**
     * Test that phpt, pht is invalid extension type
     * @dataProvider isValidDataProvider
     */
    public function testIsValid($extension)
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var NotProtectedExtension $model */
        $model = $objectManager->create(NotProtectedExtension::class);
        $this->assertFalse($model->isValid($extension));
    }

    public function isValidDataProvider()
    {
        return [
            ['phpt'],
            ['pht']
        ];
    }
}
