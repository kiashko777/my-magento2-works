<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Console\Command;

use InvalidArgumentException;
use LogicException;
use Magento\Catalog\Model\Indexer\Product\Price\DimensionModeConfiguration;
use Magento\Framework\Console\Cli;
use Magento\Framework\ObjectManagerInterface;
use Magento\Indexer\Console\Command\IndexerSetDimensionsModeCommand;
use Magento\TestFramework\App\Config;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Test command that sets indexer mode for catalog_product_price indexer
 *
 * @magentoDbIsolation disabled
 */
class PriceIndexerDimensionsModeSetCommandTest extends TestCase
{
    /** @var  ObjectManagerInterface */
    private $objectManager;

    /** @var  IndexerSetDimensionsModeCommand */
    private $command;

    /** @var  CommandTester */
    private $commandTester;

    /**
     * setUpBeforeClass
     */
    public static function setUpBeforeClass(): void
    {
        $db = Bootstrap::getInstance()->getBootstrap()
            ->getApplication()
            ->getDbInstance();
        if (!$db->isDbDumpExists()) {
            throw new LogicException('DB dump does not exist.');
        }
        $db->restoreFromDbDump();

        parent::setUpBeforeClass();
    }

    /**
     * @magentoAppArea Adminhtml
     * @magentoAppIsolation enabled
     *
     * @param string $previousMode
     * @param string $currentMode
     * @dataProvider modesDataProvider
     */
    public function testSwitchMode($previousMode, $currentMode)
    {
        $this->commandTester->execute(
            [
                'indexer' => 'catalog_product_price',
                'mode' => $currentMode,
            ]
        );
        $expectedOutput = 'Dimensions mode for indexer "Products Price" was changed from \''
            . $previousMode . '\' to \'' . $currentMode . '\'' . PHP_EOL;

        $actualOutput = $this->commandTester->getDisplay();

        $this->assertStringContainsString($expectedOutput, $actualOutput);

        static::assertEquals(
            Cli::RETURN_SUCCESS,
            $this->commandTester->getStatusCode(),
            $this->commandTester->getDisplay(true)
        );
    }

    /**
     * Modes data provider
     * @return array
     */
    public function modesDataProvider()
    {
        return [
            [DimensionModeConfiguration::DIMENSION_NONE, DimensionModeConfiguration::DIMENSION_WEBSITE],
            [DimensionModeConfiguration::DIMENSION_WEBSITE, DimensionModeConfiguration::DIMENSION_CUSTOMER_GROUP],
            [
                DimensionModeConfiguration::DIMENSION_CUSTOMER_GROUP,
                DimensionModeConfiguration::DIMENSION_WEBSITE_AND_CUSTOMER_GROUP
            ],
            [
                DimensionModeConfiguration::DIMENSION_WEBSITE_AND_CUSTOMER_GROUP,
                DimensionModeConfiguration::DIMENSION_NONE
            ],
            [
                DimensionModeConfiguration::DIMENSION_NONE,
                DimensionModeConfiguration::DIMENSION_WEBSITE_AND_CUSTOMER_GROUP
            ],
            [
                DimensionModeConfiguration::DIMENSION_WEBSITE_AND_CUSTOMER_GROUP,
                DimensionModeConfiguration::DIMENSION_CUSTOMER_GROUP
            ],
            [DimensionModeConfiguration::DIMENSION_CUSTOMER_GROUP, DimensionModeConfiguration::DIMENSION_WEBSITE],
            [DimensionModeConfiguration::DIMENSION_WEBSITE, DimensionModeConfiguration::DIMENSION_NONE],
        ];
    }

    /**
     * @magentoAppArea Adminhtml
     * @magentoAppIsolation enabled
     */
    public function testSwitchModeForSameMode()
    {
        $this->commandTester->execute(
            [
                'indexer' => 'catalog_product_price',
                'mode' => DimensionModeConfiguration::DIMENSION_NONE
            ]
        );
        $expectedOutput = 'Dimensions mode for indexer "Products Price" has not been changed' . PHP_EOL;

        $actualOutput = $this->commandTester->getDisplay();

        $this->assertStringContainsString($expectedOutput, $actualOutput);

        static::assertEquals(
            Cli::RETURN_SUCCESS,
            $this->commandTester->getStatusCode(),
            $this->commandTester->getDisplay(true)
        );
    }

    /**
     * @magentoAppArea Adminhtml
     * @magentoAppIsolation enabled
     *
     */
    public function testSwitchModeWithInvalidArgument()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->commandTester->execute(
            [
                'indexer' => 'indexer_not_valid'
            ]
        );
    }

    /**
     * setUp
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->objectManager->get(Config::class)->clean();

        $this->command = $this->objectManager->create(
            IndexerSetDimensionsModeCommand::class
        );

        $this->commandTester = new CommandTester($this->command);

        parent::setUp();
    }
}
