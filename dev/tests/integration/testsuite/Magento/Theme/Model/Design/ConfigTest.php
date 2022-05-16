<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Model\Design;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Design\Config\Storage;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Theme\Model\Design\Config\Storage.
 */
class ConfigTest extends TestCase
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * Test design/header/welcome if it is saved in db as empty(null) it should be shown on backend as empty.
     *
     * @magentoDataFixture Magento/Theme/_files/config_data.php
     */
    public function testLoad()
    {
        $data = $this->storage->load('stores', 1);
        foreach ($data->getExtensionAttributes()->getDesignConfigData() as $configData) {
            if ($configData->getPath() == 'design/header/welcome') {
                $this->assertSame('', $configData->getValue());
            }
        }
    }

    protected function setUp(): void
    {
        $this->storage = Bootstrap::getObjectManager()->create(
            Storage::class
        );
    }
}
