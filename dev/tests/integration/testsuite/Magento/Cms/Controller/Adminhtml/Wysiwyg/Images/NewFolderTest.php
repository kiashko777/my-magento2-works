<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cms\Controller\Adminhtml\Wysiwyg\Images;

use Magento\Cms\Helper\Wysiwyg\Images;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Cms\Controller\Adminhtml\Wysiwyg\Images\NewFolder class.
 *
 * @magentoAppArea Adminhtml
 */
class NewFolderTest extends TestCase
{
    /**
     * @var NewFolder
     */
    private $model;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var string
     */
    private $fullDirectoryPath;

    /**
     * @var string
     */
    private $dirName = 'NewDirectory';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Images
     */
    private $imagesHelper;

    /**
     * @inheritdoc
     */
    public static function tearDownAfterClass(): void
    {
        $filesystem = Bootstrap::getObjectManager()
            ->get(Filesystem::class);
        /** @var WriteInterface $directory */
        $directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        if ($directory->isExist('wysiwyg')) {
            $directory->delete('wysiwyg');
        }
    }

    /**
     * Execute method with correct directory path to check that new folder can be created under WYSIWYG media directory.
     *
     * @return void
     */
    public function testExecute()
    {
        $this->model->getRequest()->setMethod('POST')
            ->setPostValue('name', $this->dirName);
        $this->model->getStorage()->getSession()->setCurrentPath($this->fullDirectoryPath);
        $this->model->execute();

        $this->assertTrue(
            $this->mediaDirectory->isExist(
                $this->mediaDirectory->getRelativePath(
                    $this->fullDirectoryPath . DIRECTORY_SEPARATOR . $this->dirName
                )
            )
        );
    }

    /**
     * Execute method with correct directory path to check that new folder can be created under WYSIWYG media directory.
     *
     * @magentoDataFixture Magento/Cms/_files/linked_media.php
     */
    public function testExecuteWithLinkedMedia()
    {
        $linkedDirectoryPath = $this->filesystem->getDirectoryRead(DirectoryList::PUB)
                ->getAbsolutePath() . 'linked_media';
        $this->model->getRequest()->setMethod('POST')
            ->setPostValue('name', $this->dirName);
        $this->model->getStorage()
            ->getSession()
            ->setCurrentPath($this->fullDirectoryPath . DIRECTORY_SEPARATOR . 'wysiwyg');
        $this->model->execute();

        $this->assertTrue(is_dir($linkedDirectoryPath . DIRECTORY_SEPARATOR . $this->dirName));
    }

    /**
     * Execute method with traversal directory path to check that there is no ability to create new folder not
     * under media directory.
     *
     * @return void
     */
    public function testExecuteWithWrongPath()
    {
        $dirPath = '/../../../';
        $this->model->getRequest()->setMethod('POST')
            ->setPostValue('name', $this->dirName);
        $this->model->getStorage()->getSession()->setCurrentPath($this->fullDirectoryPath . $dirPath);
        $this->model->execute();

        $this->assertFileDoesNotExist(
            $this->fullDirectoryPath . $dirPath . $this->dirName
        );
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->filesystem = $objectManager->get(Filesystem::class);
        /** @var Images $imagesHelper */
        $this->imagesHelper = $objectManager->get(Images::class);
        $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->fullDirectoryPath = $this->imagesHelper->getStorageRoot();
        $this->model = $objectManager->get(NewFolder::class);
    }
}
