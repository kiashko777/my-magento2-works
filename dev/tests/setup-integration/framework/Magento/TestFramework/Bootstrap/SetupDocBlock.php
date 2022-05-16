<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Bootstrap;

use Magento\TestFramework\Annotation\Cache;
use Magento\TestFramework\Annotation\ComponentRegistrarFixture;
use Magento\TestFramework\Annotation\CopyModules;
use Magento\TestFramework\Annotation\DataProviderFromFile;
use Magento\TestFramework\Annotation\ReinstallInstance;
use Magento\TestFramework\Annotation\SchemaFixture;
use Magento\TestFramework\Application;
use Magento\TestFramework\Isolation\WorkingDirectory;
use Magento\TestFramework\Workaround\CacheClean;
use Magento\TestFramework\Workaround\Cleanup\StaticProperties;
use Magento\TestFramework\Workaround\Cleanup\TestCaseProperties;
use Magento\TestFramework\Workaround\DeploymentConfig;
use Magento\TestFramework\Workaround\Segfault;

/**
 * Bootstrap of the custom DocBlock annotations.
 *
 * \Magento\TestFramework\Isolation\DeploymentConfig was excluded for setup/upgrade tests.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SetupDocBlock extends DocBlock
{
    /**
     * Get list of subscribers. In addition, register <b>reinstallMagento</b> annotation processing.
     *
     * @param Application $application
     * @return array
     */
    protected function _getSubscribers(Application $application)
    {
        return [
            new Segfault(),
            new TestCaseProperties(),
            new StaticProperties(),
            new WorkingDirectory(),
            new DeploymentConfig(),
            new ComponentRegistrarFixture($this->_fixturesBaseDir),
            new SchemaFixture($this->_fixturesBaseDir),
            new Cache(),
            new CacheClean(),
            new ReinstallInstance($application),
            new CopyModules(),
            new DataProviderFromFile()
        ];
    }
}
