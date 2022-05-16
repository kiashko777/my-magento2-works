<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Cms\Controller\Adminhtml\Wysiwyg\Images;

use Magento\Cms\Helper\Wysiwyg\Images;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\HttpFactory as ResponseFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Cms\Controller\Adminhtml\Wysiwyg\Images\DeleteFolder class.
 *
 * @magentoAppArea Adminhtml
 */
class DeleteFolderTest extends TestCase
{
    /**
     * @var DeleteFolder
     */
    private $model;

    /**
     * @var  Images
     */
    private $imagesHelper;

    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var string
     */
    private $fullDirectoryPath;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var HttpFactory
     */
    private $responseFactory;

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
        if ($directory->isExist('downloadable')) {
            $directory->delete('downloadable');
        }
    }

    /**
     * Execute method with correct directory path to check that directories under WYSIWYG media directory
     * can be removed.
     *
     * @return void
     * @magentoAppIsolation enabled
     */
    public function testExecute()
    {
        $directoryName = DIRECTORY_SEPARATOR . 'NewDirectory';
        $this->mediaDirectory->create(
            $this->mediaDirectory->getRelativePath($this->fullDirectoryPath . $directoryName)
        );
        $this->model->getRequest()->setParams(['node' => $this->imagesHelper->idEncode($directoryName)]);
        $this->model->getRequest()->setMethod('POST');
        $this->model->execute();

        $this->assertFalse(
            $this->mediaDirectory->isExist(
                $this->mediaDirectory->getRelativePath(
                    $this->fullDirectoryPath . $directoryName
                )
            )
        );
    }

    /**
     * Execute method with correct directory path to check that directories under linked media directory
     * can be removed.
     *
     * @magentoDataFixture Magento/Cms/_files/linked_media.php
     * @magentoAppIsolation enabled
     */
    public function testExecuteWithLinkedMedia()
    {
        $linkedDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::PUB);
        $linkedDirectoryPath = $this->filesystem->getDirectoryRead(DirectoryList::PUB)
                ->getAbsolutePath() . 'linked_media';
        $directoryName = 'NewDirectory';

        $linkedDirectory->create(
            $linkedDirectory->getRelativePath($linkedDirectoryPath . DIRECTORY_SEPARATOR . $directoryName)
        );
        $this->model->getRequest()->setParams(
            ['node' => $this->imagesHelper->idEncode('wysiwyg' . DIRECTORY_SEPARATOR . $directoryName)]
        );
        $this->model->execute();
        $this->assertFalse(is_dir($linkedDirectoryPath . DIRECTORY_SEPARATOR . $directoryName));
    }

    /**
     * Execute method with traversal directory path to check that there is no ability to remove folder which is not
     * under media directory.
     *
     * @return void
     * @magentoAppIsolation enabled
     */
    public function testExecuteWithWrongDirectoryName()
    {
        $directoryName = '/../../etc/';
        $this->model->getRequest()->setParams(['node' => $this->imagesHelper->idEncode($directoryName)]);
        $this->model->execute();

        $this->assertFileExists($this->fullDirectoryPath . $directoryName);
    }

    /**
     * Execute method to check that there is no ability to remove folder which is in excluded directories list.
     *
     * @return void
     * @magentoAppIsolation enabled
     */
    public function testExecuteWithExcludedDirectoryName()
    {
        $directoryName = 'downloadable';
        $expectedResponseMessage = 'We cannot delete directory /downloadable.';
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $mediaDirectory->create($directoryName);
        $this->assertFileExists($this->fullDirectoryPath . $directoryName);

        $this->model->getRequest()->setParams(['node' => $this->imagesHelper->idEncode($directoryName)]);
        $this->model->getRequest()->setMethod('POST');
        $jsonResponse = $this->model->execute();
        $jsonResponse->renderResult($response = $this->responseFactory->create());
        $data = json_decode($response->getBody(), true);

        $this->assertTrue($data['error']);
        $this->assertEquals($expectedResponseMessage, $data['message']);
        $this->assertFileExists($this->fullDirectoryPath . $directoryName);
    }

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->filesystem = $objectManager->get(Filesystem::class);
        $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        /** @var Images $imagesHelper */
        $this->imagesHelper = $objectManager->get(Images::class);
        $this->fullDirectoryPath = $this->imagesHelper->getStorageRoot();
        $this->responseFactory = $objectManager->get(ResponseFactory::class);
        $this->model = $objectManager->get(DeleteFolder::class);
    }
}
