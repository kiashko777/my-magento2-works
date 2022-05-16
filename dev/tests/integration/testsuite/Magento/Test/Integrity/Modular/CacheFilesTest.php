<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Magento\Framework\Cache\Config\Reader;
use Magento\Framework\Config\ValidationStateInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class CacheFilesTest extends TestCase
{
    /**
     * @param string $area
     * @dataProvider cacheConfigDataProvider
     */
    public function testCacheConfig($area)
    {
        $validationStateMock = $this->createMock(ValidationStateInterface::class);
        $validationStateMock->expects($this->any())->method('isValidationRequired')->willReturn(true);

        $objectManager = Bootstrap::getObjectManager();

        /** @var Reader $reader */
        $reader = $objectManager->create(
            Reader::class,
            ['validationState' => $validationStateMock]
        );
        try {
            $reader->read($area);
        } catch (LocalizedException $exception) {
            $this->fail($exception->getMessage());
        }
    }

    public function cacheConfigDataProvider()
    {
        return ['global' => ['global'], 'Adminhtml' => ['Adminhtml'], 'frontend' => ['frontend']];
    }
}
