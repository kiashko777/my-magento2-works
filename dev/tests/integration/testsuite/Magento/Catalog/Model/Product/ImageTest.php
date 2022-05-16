<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Product;

use Magento\Catalog\Model\View\Asset\Placeholder;
use Magento\Framework\View\FileSystem;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class \Magento\Catalog\Model\Products\ImageTest
 * @magentoAppArea frontend
 */
class ImageTest extends TestCase
{
    /**
     * @return Image
     */
    public function testSetBaseFilePlaceholder()
    {
        /** @var $model Image */
        $model = Bootstrap::getObjectManager()->create(
            Image::class
        );
        /** @var Placeholder $defaultPlaceholder */
        $defaultPlaceholder = Bootstrap::getObjectManager()
            ->create(
                Placeholder::class,
                ['type' => 'image']
            );

        $model->setDestinationSubdir('image');
        $model->setBaseFile('');
        $this->assertEquals($defaultPlaceholder->getSourceFile(), $model->getBaseFile());
        return $model;
    }

    /**
     * @param Image $model
     * @depends testSetBaseFilePlaceholder
     */
    public function testSaveFilePlaceholder($model)
    {
        $processor = $this->createPartialMock(\Magento\Framework\Image::class, ['save']);
        $processor->expects($this->exactly(0))->method('save');
        $model->setImageProcessor($processor)->saveFile();
    }

    /**
     * @param Image $model
     * @depends testSetBaseFilePlaceholder
     */
    public function testGetUrlPlaceholder($model)
    {
        $this->assertStringMatchesFormat(
            'http://localhost/static/%s/frontend/%s/Magento_Catalog/images/product/placeholder/image.jpg',
            $model->getUrl()
        );
    }

    public function testSetWatermark()
    {
        $inputFile = 'watermark.png';
        $expectedFile = '/somewhere/watermark.png';

        /** @var FileSystem|MockObject $viewFilesystem */
        $viewFileSystem = $this->createMock(FileSystem::class);
        $viewFileSystem->expects($this->once())
            ->method('getStaticFileName')
            ->with($inputFile)
            ->willReturn($expectedFile);

        /** @var $model Image */
        $model = Bootstrap::getObjectManager()
            ->create(Image::class, ['viewFileSystem' => $viewFileSystem]);
        $processor = $this->createPartialMock(
            \Magento\Framework\Image::class,
            [
                'save',
                'keepAspectRatio',
                'keepFrame',
                'keepTransparency',
                'constrainOnly',
                'backgroundColor',
                'quality',
                'setWatermarkPosition',
                'setWatermarkImageOpacity',
                'setWatermarkWidth',
                'setWatermarkHeight',
                'watermark'
            ]
        );
        $processor->expects($this->once())
            ->method('watermark')
            ->with($expectedFile);
        $model->setImageProcessor($processor);

        $model->setWatermark('watermark.png');
    }
}
