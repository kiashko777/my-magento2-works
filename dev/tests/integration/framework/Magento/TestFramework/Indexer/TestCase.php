<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Indexer;

use LogicException;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Helper\Bootstrap;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var bool
     */
    protected static $dbRestored = false;

    /**
     * @inheritDoc
     *
     * @return void
     * @throws LocalizedException
     */
    public static function tearDownAfterClass(): void
    {
        if (empty(static::$dbRestored)) {
            self::restoreFromDb();
        }
    }

    /**
     * Restore DB data after test execution.
     *
     * @throws LocalizedException
     */
    protected static function restoreFromDb(): void
    {
        $db = Bootstrap::getInstance()->getBootstrap()
            ->getApplication()
            ->getDbInstance();
        if (!$db->isDbDumpExists()) {
            throw new LogicException('DB dump does not exist.');
        }
        $db->restoreFromDbDump();
    }
}
