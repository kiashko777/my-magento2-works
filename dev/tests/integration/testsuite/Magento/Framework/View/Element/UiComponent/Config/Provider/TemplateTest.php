<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\View\Element\UiComponent\Config\Provider;

use Magento\Framework\App\Arguments\ValidationState;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme\Registration;
use PHPUnit\Framework\TestCase;

/**
 * @magentoComponentsDir Magento/Framework/View/_files/UiComponent/theme
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class TemplateTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Template
     */
    private $model;

    public function testGetTemplate()
    {
        $expected = file_get_contents(__DIR__ . '/../../../../_files/UiComponent/expected/config.xml');

        Bootstrap::getInstance()->loadArea('Adminhtml');
        $this->objectManager->get(DesignInterface::class)
            ->setDesignTheme('FrameworkViewUiComponent/default');

        $resultOne = $this->model->getTemplate('test.xml');
        $resultTwo = $this->model->getTemplate('test.xml');

        $this->assertXmlStringEqualsXmlString($expected, $resultOne);
        $this->assertXmlStringEqualsXmlString($expected, $resultTwo);
    }

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->registerThemes();
        $this->objectManager->addSharedInstance(
            $this->objectManager->create(
                ValidationState::class,
                ['appMode' => 'default']
            ),
            ValidationState::class
        );
        $this->model = $this->objectManager->create(
            Template::class
        );
    }

    /**
     * Register themes in the fixture folder
     */
    protected function registerThemes()
    {
        /** @var Registration $registration */
        $registration = $this->objectManager->get(
            Registration::class
        );
        $registration->register();
    }

    protected function tearDown(): void
    {
        $this->objectManager->removeSharedInstance(
            ValidationState::class
        );
    }
}
