<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Model\Rate;

use LogicException;
use Magento\Tax\Model\Calculation\Rate;
use Magento\Tax\Model\ResourceModel\Calculation\Rate\Collection;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase;

class SourceTest extends TestCase
{
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
     * @magentoDbIsolation disabled
     */
    public function testToOptionArray()
    {
        /** @var Collection $collection */
        $collection = Bootstrap::getObjectManager()->get(
            Collection::class
        );

        $taxRateProvider = Bootstrap::getObjectManager()->get(Provider::class);
        $expectedResult = [];
        /** @var $taxRate Rate */
        foreach ($collection as $taxRate) {
            $expectedResult[] = ['value' => $taxRate->getId(), 'label' => $taxRate->getCode()];
            if (count($expectedResult) >= $taxRateProvider->getPageSize()) {
                break;
            }
        }

        /** @var Source $source */
        if (empty($expectedResult)) {
            $this->fail('Preconditions failed: At least one tax rate should be available.');
        }
        $source = Bootstrap::getObjectManager()->get(Source::class);
        $this->assertEquals(
            $expectedResult,
            $source->toOptionArray(),
            'Tax rate options are invalid.'
        );
    }

    /**
     * teardown
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
