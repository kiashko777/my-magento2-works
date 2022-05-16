<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Magento\Framework\App\Utility\Files;
use Magento\Sales\Model\Config\SchemaLocator;
use Magento\TestFramework\Helper\Bootstrap;

class SalesConfigFilesTest extends AbstractMergedConfigTest
{
    /**
     * attributes represent merging rules
     * copied from original namespace Magento\Framework\App\Route\Config;
     *
     * class Reader
     *
     * @var array
     */
    protected function getIdAttributes()
    {
        return [
            '/config/section' => 'name',
            '/config/section/group' => 'name',
            '/config/section/group/item' => 'name',
            '/config/section/group/item/renderer' => 'name',
            '/config/order/available_product_type' => 'name',
        ];
    }

    /**
     * Path to tough XSD for merged file validation
     *
     * @var string
     */
    protected function getMergedSchemaFile()
    {
        $objectManager = Bootstrap::getObjectManager();
        return $objectManager->get(SchemaLocator::class)->getSchema();
    }

    protected function getConfigFiles()
    {
        return Files::init()->getConfigFiles('sales.xml');
    }
}
