<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Translate;

use DOMDocument;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class InlineTest extends TestCase
{
    /**
     * @var Inline
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_storeId = 'default';

    /**
     * @var StateInterface
     */
    protected $state;

    public static function setUpBeforeClass(): void
    {
        Bootstrap::getObjectManager()->get(State::class)
            ->setAreaCode('frontend');
        Bootstrap::getObjectManager()->get(
            DesignInterface::class
        )->setDesignTheme(
            'Magento/blank'
        );
    }

    public function testIsAllowed()
    {
        $this->assertTrue($this->_model->isAllowed());
        $this->assertTrue($this->_model->isAllowed($this->_storeId));
        $this->assertTrue(
            $this->_model->isAllowed(
                Bootstrap::getObjectManager()->get(
                    StoreManagerInterface::class
                )->getStore(
                    $this->_storeId
                )
            )
        );
        $this->state->suspend();
        $this->assertFalse($this->_model->isAllowed());
        $this->assertFalse($this->_model->isAllowed($this->_storeId));
        $this->assertFalse(
            $this->_model->isAllowed(
                Bootstrap::getObjectManager()->get(
                    StoreManagerInterface::class
                )->getStore(
                    $this->_storeId
                )
            )
        );
    }

    /**
     * @param string $originalText
     * @param string $expectedText
     * @dataProvider processResponseBodyDataProvider
     */
    public function testProcessResponseBody($originalText, $expectedText)
    {
        $actualText = $originalText;
        $this->_model->processResponseBody($actualText, false);
        $this->markTestIncomplete('Bug MAGE-2494');

        $expected = new DOMDocument();
        $expected->preserveWhiteSpace = false;
        $expected->loadHTML($expectedText);

        $actual = new DOMDocument();
        $actual->preserveWhiteSpace = false;
        $actual->loadHTML($actualText);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function processResponseBodyDataProvider()
    {
        $originalText = file_get_contents(__DIR__ . '/_files/_inline_page_original.html');
        $expectedText = file_get_contents(__DIR__ . '/_files/_inline_page_expected.html');

        $package = Bootstrap::getObjectManager()->get(
            DesignInterface::class
        )->getDesignTheme()->getPackageCode();
        $expectedText = str_replace('{{design_package}}', $package, $expectedText);
        return [
            'plain text' => ['text with no translations and tags', 'text with no translations and tags'],
            'html string' => [$originalText, $expectedText],
            'html array' => [[$originalText], [$expectedText]]
        ];
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Inline::class
        );
        $this->state = Bootstrap::getObjectManager()->get(
            StateInterface::class
        );
        /* Called getConfig as workaround for setConfig bug */
        Bootstrap::getObjectManager()->get(
            StoreManagerInterface::class
        )->getStore(
            $this->_storeId
        )->getConfig(
            'dev/translate_inline/active'
        );
        Bootstrap::getObjectManager()->get(
            MutableScopeConfigInterface::class
        )->setValue(
            'dev/translate_inline/active',
            true,
            ScopeInterface::SCOPE_STORE,
            $this->_storeId
        );
    }
}
