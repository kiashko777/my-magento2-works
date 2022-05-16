<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\PageCache\Model\Layout;

use LogicException;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\View\EntitySpecificHandlesList;
use Magento\Framework\View\Model\Layout\Merge;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class MergeTest extends TestCase
{
    /**
     * @magentoAppArea frontend
     */
    public function testLoadEntitySpecificHandleWithEsiBlock()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Handle \'default\' must not contain blocks with \'ttl\' attribute specified');

        $objectManager = Bootstrap::getObjectManager();

        // Mock cache to avoid layout being read from existing cache
        $cacheMock = $this->createMock(FrontendInterface::class);
        /** @var Merge $layoutMerge */
        $layoutMerge = $objectManager->create(
            Merge::class,
            ['cache' => $cacheMock]
        );

        /** @var EntitySpecificHandlesList $entitySpecificHandleList */
        $entitySpecificHandleList = $objectManager->get(EntitySpecificHandlesList::class);
        // Add 'default' handle, which has declarations of blocks with ttl, to the list of entity specific handles.
        // This allows to simulate a situation, when block with ttl attribute
        // is declared e.g. in 'catalog_product_view_id_1' handle
        $entitySpecificHandleList->addHandle('default');
        $layoutMerge->load(['default']);
    }
}
