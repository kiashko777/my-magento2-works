<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Webapi\Product\Option\Type\File;

use Magento\Framework\Api\Data\ImageContentInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class ProcessorTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @dataProvider pathConfigDataProvider
     */
    public function testProcessFileContent($pathConfig)
    {
        $model = $this->getModel($pathConfig);
        /** @var ImageContentInterface $imageContent */
        $imageContent = $this->objectManager->create(
            ImageContentInterface::class
        );
        $imageContent->setName('my_file');
        $imageContent->setType('image/png');
        $imageContent->setBase64EncodedData($this->getImageContent());
        $result = $model->processFileContent($imageContent);

        $this->assertArrayHasKey('fullpath', $result);
        $this->assertFileExists($result['fullpath']);

        /** @var  $filesystem Filesystem */
        $filesystem = $this->objectManager->get(Filesystem::class);
        $this->assertArrayHasKey('quote_path', $result);
        $filePath = $filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($result['quote_path']);
        $this->assertFileExists($filePath);

        $this->assertArrayHasKey('order_path', $result);
        $filePath = $filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath($result['order_path']);
        $this->assertFileExists($filePath);
    }

    /**
     * @return Processor
     */
    private function getModel($pathConfig)
    {
        $rootPath = Bootstrap::getInstance()->getAppTempDir();
        $dirList = $this->objectManager->create(
            DirectoryList::class,
            ['root' => $rootPath, 'config' => $pathConfig]
        );
        $fileSystem = $this->objectManager->create(
            Filesystem::class,
            ['directoryList' => $dirList]
        );
        $model = $this->objectManager->create(
            Processor::class,
            ['filesystem' => $fileSystem]
        );
        return $model;
    }

    /**
     * Black rectangle 10x10px
     *
     * @return string
     */
    private function getImageContent()
    {
        return 'iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKEAIAAABSwISpAAAACXBIWXMAAABIAAAASABGyWs+AAAACXZwQWcAAAA' .
            'KAAAACgBOpnblAAAAD0lEQVQoz2NgGAWjgJYAAAJiAAEQ3MCgAAAAJXRFWHRjcmVhdGUtZGF0ZQAyMDA5LTA3LTA4VDE5Oj' .
            'E1OjMyKzAyOjAwm1PZQQAAACV0RVh0bW9kaWZ5LWRhdGUAMjAwOS0wNy0wOFQxOToxNTozMiswMjowMMTir3UAAAAASUVORK5CYII=';
    }

    public function pathConfigDataProvider()
    {
        return [
            // default config
            [[]],
            // config from pub/index.php
            [
                [
                    DirectoryList::PUB => [DirectoryList::URL_PATH => ''],
                    DirectoryList::MEDIA => [DirectoryList::URL_PATH => 'media'],
                    DirectoryList::STATIC_VIEW => [DirectoryList::URL_PATH => 'static'],
                    DirectoryList::UPLOAD => [DirectoryList::URL_PATH => 'media/upload'],
                ]
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
