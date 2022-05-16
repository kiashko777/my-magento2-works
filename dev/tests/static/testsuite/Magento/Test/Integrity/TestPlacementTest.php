<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test to ensure that readme file present in specified directories
 */

namespace Magento\Test\Integrity;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\Data\Collection\Filesystem;
use PHPUnit\Framework\TestCase;

class TestPlacementTest extends TestCase
{
    /** @var array */
    private $scanList = ['dev/tests/unit/testsuite/Magento'];

    /**
     * @var string Path to project root
     */
    private $root;

    public function testUnitTestFilesPlacement()
    {
        $objectManager = Bootstrap::create(BP, $_SERVER)->getObjectManager();
        /** @var Filesystem $filesystem */
        $filesystem = $objectManager->get(Filesystem::class);
        $filesystem->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setCollectRecursively(true);

        $targetsExist = false;
        foreach ($this->scanList as $dir) {
            if (realpath($this->root . DIRECTORY_SEPARATOR . $dir)) {
                $filesystem->addTargetDir($this->root . DIRECTORY_SEPARATOR . $dir);
                $targetsExist = true;
            }
        }

        if ($targetsExist) {
            $files = $filesystem->load()->toArray();
            $fileList = [];
            foreach ($files['items'] as $file) {
                $fileList[] = $file['filename'];
            }

            $this->assertEquals(
                0,
                $files['totalRecords'],
                "The following files have been found in obsolete test directories: \n"
                . implode("\n", $fileList)
            );
        }
    }

    protected function setUp(): void
    {
        $this->root = BP;
    }
}
