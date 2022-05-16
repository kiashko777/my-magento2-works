<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Config\App\Config\Type;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDataFixture Magento/Config/_files/config_data.php
 * @magentoAppIsolation enabled
 */
class SystemTest extends TestCase
{
    /**
     * @var System
     */
    private $system;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function testGetValueDefaultScope()
    {
        $this->assertEquals(
            'value1.db.default.test',
            $this->system->get('default/web/test/test_value_1')
        );

        $this->assertEquals(
            'value1.db.website_base.test',
            $this->system->get('websites/base/web/test/test_value_1')
        );

        $this->assertEquals(
            'value1.db.store_default.test',
            $this->system->get('stores/default/web/test/test_value_1')
        );
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->system = $this->objectManager->create(System::class);
    }
}
