<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Console\Command;

use LogicException;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Console\Cli;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Indexer\Console\Command\IndexerReindexCommand;
use Magento\Setup\Fixtures\FixtureModel;
use Magento\TestFramework\App\Config;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class GenerateFixturesCommandCommandTest
 *
 * @magentoDbIsolation disabled
 */
class GenerateFixturesCommandTest extends TestCase
{
    /** @var  CommandTester */
    private $indexerCommand;

    /** @var  FixtureModel */
    private $fixtureModelMock;

    /** @var  ObjectManagerInterface */
    private $objectManager;

    /** @var  GenerateFixturesCommand */
    private $command;

    /** @var  CommandTester */
    private $commandTester;

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
     */
    public function testExecute()
    {
        $profile = realpath(__DIR__ . "/_files/min_profile.xml");
        $this->commandTester->execute(
            [
                GenerateFixturesCommand::PROFILE_ARGUMENT => $profile,
                '--' . GenerateFixturesCommand::SKIP_REINDEX_OPTION => true
            ]
        );
        $this->indexerCommand->execute([]);

        static::assertEquals(
            Cli::RETURN_SUCCESS,
            $this->indexerCommand->getStatusCode(),
            $this->indexerCommand->getDisplay(true)
        );

        static::assertEquals(
            Cli::RETURN_SUCCESS,
            $this->commandTester->getStatusCode(),
            $this->commandTester->getDisplay(true)
        );
    }

    /**
     * Setup
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();

        $this->objectManager->get(Config::class)->clean();

        $this->fixtureModelMock = $this->getMockBuilder(FixtureModel::class)
            ->setMethods(['getObjectManager'])
            ->setConstructorArgs([$this->objectManager->get(IndexerReindexCommand::class)])
            ->getMock();
        $this->fixtureModelMock
            ->method('getObjectManager')
            ->willReturn($this->objectManager);

        $this->command = $this->objectManager->create(
            GenerateFixturesCommand::class,
            [
                'fixtureModel' => $this->fixtureModelMock
            ]
        );

        $objectFactoryMock = $this->getMockBuilder(ObjectManagerFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $objectFactoryMock
            ->method('create')
            ->willReturn($this->objectManager);

        $this->indexerCommand = new CommandTester($this->objectManager->create(
            IndexerReindexCommand::class,
            ['objectManagerFactory' => $objectFactoryMock]
        ));

        $this->commandTester = new CommandTester($this->command);

        $this->setIncrement(3);

        parent::setUp();
    }

    /**
     * @param $value
     */
    private function setIncrement($value)
    {
        /** @var AdapterInterface $db */
        $db = Bootstrap::getObjectManager()->get(ResourceConnection::class)->getConnection();
        $db->query("SET @@session.auto_increment_increment=$value");
        $db->query("SET @@session.auto_increment_offset=$value");
    }

    /**
     * teardown
     */
    protected function tearDown(): void
    {
        $this->setIncrement(1);

        self::restoreFromDb();
        self::$dbRestored = true;

        parent::tearDown();
    }
}
