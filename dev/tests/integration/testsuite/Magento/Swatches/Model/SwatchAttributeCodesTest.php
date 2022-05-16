<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Swatches\Model;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class SwatchAttributeCodesTest extends TestCase
{
    /** @var  SwatchAttributeCodes */
    private $swatchAttributeCodes;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture Magento/Swatches/_files/swatch_attribute.php
     */
    public function testGetCodes()
    {
        $attribute = $this->objectManager
            ->create(Attribute::class)
            ->load('color_swatch', 'attribute_code');
        $expected = [
            $attribute->getAttributeId() => $attribute->getAttributeCode()
        ];
        $swatchAttributeCodes = $this->swatchAttributeCodes->getCodes();

        $this->assertEquals($expected, $swatchAttributeCodes);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->swatchAttributeCodes = $this->objectManager->create(
            SwatchAttributeCodes::class
        );
    }
}
