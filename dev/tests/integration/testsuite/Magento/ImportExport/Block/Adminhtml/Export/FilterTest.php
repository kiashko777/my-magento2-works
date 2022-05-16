<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\ImportExport\Block\Adminhtml\Export\Filter
 */

namespace Magento\ImportExport\Block\Adminhtml\Export;

use IntlDateFormatter;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class FilterTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetDateFromToHtmlWithValue()
    {
        Bootstrap::getInstance()
            ->loadArea(FrontNameResolver::AREA_CODE);
        Bootstrap::getObjectManager()->get(DesignInterface::class)
            ->setDefaultDesignTheme();
        $block = Bootstrap::getObjectManager()
            ->create(Filter::class);
        $method = new ReflectionMethod(
            Filter::class,
            '_getDateFromToHtmlWithValue'
        );
        $method->setAccessible(true);

        $arguments = [
            'data' => [
                'attribute_code' => 'date',
                'backend_type' => 'datetime',
                'frontend_input' => 'date',
                'frontend_label' => 'Date',
            ],
        ];
        $attribute = Bootstrap::getObjectManager()->create(
            Attribute::class,
            $arguments
        );
        $html = $method->invoke($block, $attribute, null);
        $this->assertNotEmpty($html);

        $dateFormat = Bootstrap::getObjectManager()->get(
            TimezoneInterface::class
        )->getDateFormat(
            IntlDateFormatter::SHORT
        );
        $pieces = array_filter(explode('<strong>', $html));
        foreach ($pieces as $piece) {
            $this->assertStringContainsString('dateFormat: "' . $dateFormat . '",', $piece);
        }
    }
}
