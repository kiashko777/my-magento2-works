<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\UrlRewrite\Block\Edit;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Model\Session;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Hidden;
use Magento\Framework\Data\Form\Element\Select;
use Magento\Framework\DataObject;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\System\Store;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\UrlRewrite\Block\Edit\FormTest
 * @magentoAppArea Adminhtml
 */
class FormTest extends TestCase
{
    /**
     * Test that form was prepared correctly
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        // Test form was configured correctly
        $form = $this->_getFormInstance(['url_rewrite' => new DataObject(['id' => 3])]);
        $this->assertInstanceOf(\Magento\Framework\Data\Form::class, $form);
        $this->assertNotEmpty($form->getAction());
        $this->assertEquals('edit_form', $form->getId());
        $this->assertEquals('post', $form->getMethod());
        $this->assertTrue($form->getUseContainer());
        $this->assertStringContainsString('/id/3', $form->getAction());

        // Check all expected form elements are present
        $expectedElements = [
            'store_id',
            'entity_type',
            'entity_id',
            'request_path',
            'target_path',
            'redirect_type',
            'description',
        ];
        foreach ($expectedElements as $expectedElement) {
            $this->assertNotNull($form->getElement($expectedElement));
        }
    }

    /**
     * Get form instance
     *
     * @param array $args
     * @return \Magento\Framework\Data\Form
     */
    protected function _getFormInstance($args = [])
    {
        /** @var $layout Layout */
        $layout = Bootstrap::getObjectManager()->get(
            LayoutInterface::class
        );
        /** @var $block Form */
        $block = $layout->createBlock(Form::class, 'block', ['data' => $args]);
        $block->setTemplate(null);
        $block->toHtml();
        return $block->getForm();
    }

    /**
     * Check session data restoring
     * @magentoAppIsolation enabled
     */
    public function testSessionRestore()
    {
        // Set urlrewrite data to session
        $sessionValues = [
            'store_id' => 1,
            'entity_type' => 'entity_type',
            'entity_id' => 'entity_id',
            'request_path' => 'request_path',
            'target_path' => 'target_path',
            'redirect_type' => 'redirect_type',
            'description' => 'description',
        ];
        Bootstrap::getObjectManager()->create(
            Session::class
        )->setUrlRewriteData(
            $sessionValues
        );
        // Re-init form to use newly set session data
        $form = $this->_getFormInstance(['url_rewrite' => new DataObject()]);

        // Check that all fields values are restored from session
        foreach ($sessionValues as $field => $value) {
            $this->assertEquals($value, $form->getElement($field)->getValue());
        }
    }

    /**
     * Test store element is hidden when only one store available
     *
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store general/single_store_mode/enabled 1
     */
    public function testStoreElementSingleStore()
    {
        $form = $this->_getFormInstance(['url_rewrite' => new DataObject(['id' => 3])]);
        /** @var $storeElement AbstractElement */
        $storeElement = $form->getElement('store_id');
        $this->assertInstanceOf(Hidden::class, $storeElement);

        // Check that store value set correctly
        $defaultStore = Bootstrap::getObjectManager()->get(
            StoreManagerInterface::class
        )->getStore(
            true
        )->getId();
        $this->assertEquals($defaultStore, $storeElement->getValue());
    }

    /**
     * Test store selection is available and correctly configured
     *
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Store/_files/core_fixturestore.php
     */
    public function testStoreElementMultiStores()
    {
        $form = $this->_getFormInstance(['url_rewrite' => new DataObject(['id' => 3])]);
        /** @var $storeElement AbstractElement */
        $storeElement = $form->getElement('store_id');

        // Check store selection elements has correct type
        $this->assertInstanceOf(Select::class, $storeElement);

        // Check store selection elements has correct renderer
        $this->assertInstanceOf(
            Element::class,
            $storeElement->getRenderer()
        );

        // Check store elements has expected values
        $storesList = Bootstrap::getObjectManager()->get(
            Store::class
        )->getStoreValuesForForm();
        $this->assertIsArray($storeElement->getValues());
        $this->assertNotEmpty($storeElement->getValues());
        $this->assertEquals($storesList, $storeElement->getValues());
    }

    /**
     * Test fields disabled status
     * @dataProvider fieldsStateDataProvider
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store general/single_store_mode/enabled 0
     */
    public function testReadonlyFields($urlRewrite, $fields)
    {
        $form = $this->_getFormInstance(['url_rewrite' => $urlRewrite]);
        foreach ($fields as $fieldKey => $expected) {
            $this->assertEquals($expected, $form->getElement($fieldKey)->getReadonly());
        }
    }

    /**
     * Data provider for checking fields state
     */
    public function fieldsStateDataProvider()
    {
        return [
            [
                new DataObject(),
                [
                    'store_id' => false,
                ],
            ],
            [
                new DataObject(['id' => 3, 'is_autogenerated' => true]),
                [
                    'store_id' => true,
                ]
            ]
        ];
    }
}
