<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Bootstrap;

use Magento\TestFramework\Annotation\AdminConfigFixture;
use Magento\TestFramework\Annotation\AppArea;
use Magento\TestFramework\Annotation\AppIsolation;
use Magento\TestFramework\Annotation\Cache;
use Magento\TestFramework\Annotation\ComponentRegistrarFixture;
use Magento\TestFramework\Annotation\ConfigFixture;
use Magento\TestFramework\Annotation\DataFixture;
use Magento\TestFramework\Annotation\DataFixtureBeforeTransaction;
use Magento\TestFramework\Annotation\DbIsolation;
use Magento\TestFramework\Annotation\IndexerDimensionMode;
use Magento\TestFramework\Application;
use Magento\TestFramework\Event\Magento;
use Magento\TestFramework\Event\PhpUnit;
use Magento\TestFramework\Event\Transaction;
use Magento\TestFramework\EventManager;
use Magento\TestFramework\Isolation\AppConfig;
use Magento\TestFramework\Isolation\DeploymentConfig;
use Magento\TestFramework\Isolation\WorkingDirectory;
use Magento\TestFramework\Workaround\Cleanup\StaticProperties;
use Magento\TestFramework\Workaround\Cleanup\TestCaseProperties;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver\TestSetter;
use Magento\TestFramework\Workaround\Segfault;

/**
 * Bootstrap of the custom DocBlock annotations
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DocBlock
{
    /**
     * @var string
     */
    protected $_fixturesBaseDir;

    /**
     * @param string $fixturesBaseDir
     */
    public function __construct($fixturesBaseDir)
    {
        $this->_fixturesBaseDir = $fixturesBaseDir;
    }

    /**
     * Activate custom DocBlock annotations along with more-or-less permanent workarounds
     *
     * @param Application $application
     */
    public function registerAnnotations(Application $application)
    {
        $eventManager = new EventManager($this->_getSubscribers($application));
        PhpUnit::setDefaultEventManager($eventManager);
        Magento::setDefaultEventManager($eventManager);
    }

    /**
     * Get list of subscribers.
     *
     * Note: order of registering (and applying) annotations is important.
     * To allow config fixtures to deal with fixture stores, data fixtures should be processed first.
     * ConfigFixture applied twice because data fixtures could clean config and clean custom settings
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
            new TestSetter(),
            new AppIsolation($application),
            new IndexerDimensionMode(),
            new AppConfig(),
            new ConfigFixture(),
            new DataFixtureBeforeTransaction(),
            new Transaction(
                new EventManager(
                    [
                        new DbIsolation(),
                        new DataFixture(),
                    ]
                )
            ),
            new ComponentRegistrarFixture($this->_fixturesBaseDir),
            new AppArea($application),
            new Cache(),
            new AdminConfigFixture(),
            new ConfigFixture(),
        ];
    }
}
