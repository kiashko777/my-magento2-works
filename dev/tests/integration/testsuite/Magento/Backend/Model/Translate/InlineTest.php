<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Backend\Model\Translate;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\UrlInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea Adminhtml
 */
class InlineTest extends TestCase
{
    /**
     * @var InlineInterface
     */
    protected $_translateInline;

    /**
     * @magentoAdminConfigFixture dev/translate_inline/active_admin 1
     * @covers \Magento\Framework\Translate\Inline::getAjaxUrl
     */
    public function testAjaxUrl()
    {
        $body = '<html><body>some body</body></html>';
        /** @var \Magento\Backend\Model\UrlInterface $url */
        $url = Bootstrap::getObjectManager()->get(UrlInterface::class);
        $url->getUrl(FrontNameResolver::AREA_CODE . '/ajax/translate');
        $this->_translateInline->processResponseBody($body, true);
        $expected = str_replace(
            [':', '/'],
            ['\u003A', '\u002F'],
            $url->getUrl(FrontNameResolver::AREA_CODE . '/ajax/translate')
        );
        $this->assertStringContainsString($expected, $body);
    }

    protected function setUp(): void
    {
        $this->_translateInline = Bootstrap::getObjectManager()->create(
            InlineInterface::class
        );
    }
}
