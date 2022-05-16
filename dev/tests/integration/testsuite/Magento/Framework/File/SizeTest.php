<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Magento file size test
 */

namespace Magento\Framework\File;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SizeTest extends TestCase
{
    /**
     * @var Size
     */
    protected $_fileSize;

    /**
     * @backupStaticAttributes
     */
    public function testGetMaxFileSize()
    {
        $this->assertGreaterThanOrEqual(0, $this->_fileSize->getMaxFileSize());
        $this->assertGreaterThanOrEqual(0, $this->_fileSize->getMaxFileSizeInMb());
    }

    protected function setUp(): void
    {
        $this->_fileSize = Bootstrap::getObjectManager()
            ->get(Size::class);
    }
}
