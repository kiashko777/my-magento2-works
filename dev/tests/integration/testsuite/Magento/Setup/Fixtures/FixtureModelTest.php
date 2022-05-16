<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Fixtures;

use AssertionError;
use LogicException;
use Magento\Eav\Model\Cache\Type;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\Cache;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\ObjectManagerInterface;
use Magento\Indexer\Console\Command\IndexerReindexCommand;
use Magento\Indexer\Model\Config;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase;

/**
 * Class Application test
 *
 * @magentoDbIsolation disabled
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FixtureModelTest extends TestCase
{
    /**
     * Profile generator working directory
     *
     * @var string
     */
    protected static $_generatorWorkingDir;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $indexersState = [];

    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var array
     */
    private $entityAsserts = [];

    public static function setUpBeforeClass(): void
    {
        $db = Bootstrap::getInstance()->getBootstrap()
            ->getApplication()
            ->getDbInstance();
        if (!$db->isDbDumpExists()) {
            throw new LogicException('DB dump does not exist.');
        }
        $db->restoreFromDbDump();

        self::$_generatorWorkingDir = realpath(
            __DIR__ . '/../../../../../../../setup/src/Magento/Setup/Fixtures/_files'
        );
        copy(
            self::$_generatorWorkingDir . '/tax_rates.csv',
            self::$_generatorWorkingDir . '/tax_rates.csv.bak'
        );
        copy(
            __DIR__ . '/_files/tax_rates.csv',
            self::$_generatorWorkingDir . '/tax_rates.csv'
        );
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        unlink(self::$_generatorWorkingDir . '/tax_rates.csv');
        rename(
            self::$_generatorWorkingDir . '/tax_rates.csv.bak',
            self::$_generatorWorkingDir . '/tax_rates.csv'
        );
        /** @var $appCache Cache */
        $appCache = Bootstrap::getObjectManager()->get(Cache::class);
        $appCache->clean(
            [
                Type::CACHE_TAG,
                Attribute::CACHE_TAG,
            ]
        );
    }

    /**
     * Generate test profile and performs assertions that generated entities are valid
     */
    public function testFixtureGeneration()
    {
        $reindexCommand = Bootstrap::getObjectManager()->get(
            IndexerReindexCommand::class
        );
        $itfApplication = Bootstrap::getInstance()->getBootstrap()->getApplication();
        $model = new FixtureModel($reindexCommand, $itfApplication->getInitParams());
        $model->loadConfig(__DIR__ . '/_files/small.xml');
        $model->initObjectManager();

        foreach ($model->loadFixtures()->getFixtures() as $fixture) {
            $fixture->execute();
        }

        foreach ($this->entityAsserts as $entityAssert) {
            try {
                $this->assertTrue($entityAssert->assert());
            } catch (AssertionError $assertionError) {
                $this->assertTrue(false, $assertionError->getMessage());
            }
        }
    }

    /**
     * Set indexer mode to "scheduled" for do not perform reindex after creation entity
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->indexerRegistry = $this->objectManager->get(IndexerRegistry::class);

        $this->entityAsserts[] = $this->objectManager->get(FixturesAsserts\SimpleProductsAssert::class);
        $this->entityAsserts[] = $this->objectManager->get(FixturesAsserts\ConfigurableProductsAssert::class);
        $this->entityAsserts[] = $this->objectManager->get(FixturesAsserts\BundleProductsAssert::class);
        $this->entityAsserts[] = $this->objectManager->get(FixturesAsserts\ImagesAssert::class);

        foreach ($this->objectManager->get(Config::class)->getIndexers() as $indexerId) {
            $indexer = $this->indexerRegistry->get($indexerId['indexer_id']);
            $this->indexersState[$indexerId['indexer_id']] = $indexer->isScheduled();
            $indexer->setScheduled(true);
        }
    }

    /**
     * Return indexer to previous state
     */
    protected function tearDown(): void
    {
        foreach ($this->indexersState as $indexerId => $state) {
            $indexer = $this->indexerRegistry->get($indexerId);
            $indexer->setScheduled($state);
        }
        self::restoreFromDb();
        self::$dbRestored = true;
    }
}
