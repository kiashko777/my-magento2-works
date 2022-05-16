<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Modular;

use Magento\Cron\Model\Config\SchemaLocator;
use Magento\Framework\App\Utility\Files;
use Magento\TestFramework\Helper\Bootstrap;

class CrontabConfigFilesTest extends AbstractMergedConfigTest
{
    /**
     * attributes represent merging rules
     * copied from original class \Magento\Framework\App\Route\Config\Reader
     *
     * @var array
     */
    protected function getIdAttributes()
    {
        return ['/config/group' => 'id', '/config/group/job' => 'name'];
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
        return Files::init()->getConfigFiles('crontab.xml');
    }
}
