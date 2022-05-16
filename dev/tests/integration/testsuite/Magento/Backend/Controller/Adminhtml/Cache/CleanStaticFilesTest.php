<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Controller\Adminhtml\Cache;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

class CleanStaticFilesTest extends AbstractBackendController
{
    public function testAclHasAccess()
    {
        // setup
        /** @var Filesystem $filesystem */
        $filesystem = Bootstrap::getObjectManager()->get(Filesystem::class);
        $dirStatic = $filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        $subStaticDir = 'subdir';
        $dirStatic->create($subStaticDir);
        $this->assertTrue($dirStatic->isExist($subStaticDir));

        $dirVar = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $subVarDir = DirectoryList::TMP_MATERIALIZATION_DIR . '/subdir';
        $dirVar->create($subVarDir);
        $this->assertTrue($dirVar->isExist($subVarDir));

        // test
        parent::testAclHasAccess();
        $this->assertSessionMessages(
            $this->containsEqual("The static files cache has been cleaned."),
            MessageInterface::TYPE_SUCCESS,
            ManagerInterface::class
        );
        $this->assertFalse($dirStatic->isExist($subStaticDir));
        $this->assertTrue($dirVar->isExist(DirectoryList::TMP_MATERIALIZATION_DIR));
        $this->assertFalse($dirVar->isExist($subVarDir));
    }

    protected function setUp(): void
    {
        $this->resource = 'Magento_Backend::cache';
        $this->uri = 'backend/admin/cache/cleanStaticFiles';
        parent::setUp();
    }
}
