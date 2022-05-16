<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Block\Html;

use Magento\Framework\App\State;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 */
class BreadcrumbsTest extends TestCase
{
    /**
     * @var Breadcrumbs
     */
    private $block;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function testAddCrumb()
    {
        $this->assertEmpty($this->block->toHtml());
        $info = ['label' => 'test label', 'title' => 'test title', 'link' => 'test link'];
        $this->block->addCrumb('test', $info);
        $html = $this->block->toHtml();
        $this->assertStringContainsString('test label', $html);
        $this->assertStringContainsString('test title', $html);
        $this->assertStringContainsString('test link', $html);
    }

    public function testGetCacheKeyInfo()
    {
        $crumbs = ['test' => ['label' => 'test label', 'title' => 'test title', 'link' => 'test link']];
        foreach ($crumbs as $crumbName => &$crumb) {
            $this->block->addCrumb($crumbName, $crumb);
            $crumb += ['first' => null, 'last' => null, 'readonly' => null];
        }

        $cacheKeyInfo = $this->block->getCacheKeyInfo();
        $crumbsFromCacheKey = $this->serializer->unserialize(base64_decode($cacheKeyInfo['crumbs']));
        $this->assertEquals($crumbs, $crumbsFromCacheKey);
    }

    protected function setUp(): void
    {
        Bootstrap::getObjectManager()->get(State::class)->setAreaCode('frontend');
        $this->block = Bootstrap::getObjectManager()
            ->get(LayoutInterface::class)
            ->createBlock(Breadcrumbs::class);
        $this->serializer = Bootstrap::getObjectManager()->get(SerializerInterface::class);
    }
}
