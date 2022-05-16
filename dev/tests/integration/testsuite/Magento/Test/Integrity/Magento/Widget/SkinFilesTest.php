<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Test\Integrity\Magento\Widget;

use Magento\Framework\View\Asset\Repository;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Widget\Model\Widget;
use Magento\Widget\Model\Widget\Instance;
use PHPUnit\Framework\TestCase;

class SkinFilesTest extends TestCase
{
    /**
     * @dataProvider widgetPlaceholderImagesDataProvider
     */
    public function testWidgetPlaceholderImages($skinImage)
    {
        /** @var Repository $assetRepo */
        $assetRepo = Bootstrap::getObjectmanager()
            ->get(Repository::class);
        $this->assertFileExists(
            $assetRepo->createAsset($skinImage, ['area' => 'Adminhtml'])->getSourceFile()
        );
    }

    /**
     * @return array
     */
    public function widgetPlaceholderImagesDataProvider()
    {
        $result = [];
        /** @var $model Widget */
        $model = Bootstrap::getObjectManager()->create(
            Widget::class
        );
        foreach ($model->getWidgetsArray() as $row) {
            /** @var $instance Instance */
            $instance = Bootstrap::getObjectManager()->create(
                Instance::class
            );
            $config = $instance->setType($row['type'])->getWidgetConfigAsArray();
            if (isset($config['placeholder_image'])) {
                $result[] = [(string)$config['placeholder_image']];
            }
        }
        return $result;
    }
}
