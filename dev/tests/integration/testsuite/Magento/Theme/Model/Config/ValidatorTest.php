<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Theme\Model\Config;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Email\Model\Template;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\TemplateInterfaceFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Api\Data\DesignConfigExtensionInterface;
use Magento\Theme\Api\Data\DesignConfigInterface;
use Magento\Theme\Model\Data\Design\Config\Data;
use Magento\Theme\Model\Design\Config\Validator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidatorTest to test \Magento\Theme\Model\Design\Config\Validator
 */
class ValidatorTest extends TestCase
{
    const TEMPLATE_CODE = 'email_exception_fixture';

    /**
     * @var Validator
     */
    private $model;

    /**
     * @var MockObject
     */
    private $templateFactoryMock;

    /**
     * @var Template
     */
    private $templateModel;

    /**
     * @magentoDataFixture Magento/Email/Model/_files/email_template.php
     */
    public function testValidateHasRecursiveReference()
    {
        $this->expectException(LocalizedException::class);

        if (!$this->templateModel->getId()) {
            $this->fail('Cannot load Template model');
        }

        $fieldConfig = [
            'path' => 'design/email/header_template',
            'fieldset' => 'other_settings/email',
            'field' => 'email_header_template'
        ];

        $designConfigMock = $this->getMockBuilder(DesignConfigInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $designConfigExtensionMock =
            $this->getMockBuilder(DesignConfigExtensionInterface::class)
                ->disableOriginalConstructor()
                ->setMethods([])
                ->getMock();
        $designElementMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $designConfigMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($designConfigExtensionMock);
        $designConfigExtensionMock->expects($this->once())
            ->method('getDesignConfigData')
            ->willReturn([$designElementMock]);
        $designElementMock->expects($this->any())->method('getFieldConfig')->willReturn($fieldConfig);
        $designElementMock->expects($this->once())->method('getPath')->willReturn($fieldConfig['path']);
        $designElementMock->expects($this->once())->method('getValue')->willReturn($this->templateModel->getId());

        $this->model->validate($designConfigMock);

        $this->expectExceptionMessage(
            'The "email_header_template" template contains an incorrect configuration, with a reference to itself. '
            . 'Remove or change the reference, then try again.'
        );
    }

    /**
     * @magentoDataFixture Magento/Email/Model/_files/email_template.php
     */
    public function testValidateNoRecursiveReference()
    {
        $this->templateFactoryMock->expects($this->once())
            ->method("create")
            ->willReturn($this->templateModel);

        $fieldConfig = [
            'path' => 'design/email/footer_template',
            'fieldset' => 'other_settings/email',
            'field' => 'email_footer_template'
        ];

        $designConfigMock = $this->getMockBuilder(DesignConfigInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $designConfigExtensionMock =
            $this->getMockBuilder(DesignConfigExtensionInterface::class)
                ->disableOriginalConstructor()
                ->setMethods([])
                ->getMock();
        $designElementMock = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $designConfigMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($designConfigExtensionMock);
        $designConfigExtensionMock->expects($this->once())
            ->method('getDesignConfigData')
            ->willReturn([$designElementMock]);
        $designElementMock->expects($this->any())->method('getFieldConfig')->willReturn($fieldConfig);
        $designElementMock->expects($this->once())->method('getPath')->willReturn($fieldConfig['path']);
        $designElementMock->expects($this->once())->method('getValue')->willReturn($this->templateModel->getId());

        $this->model->validate($designConfigMock);
    }

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(AreaList::class)
            ->getArea(FrontNameResolver::AREA_CODE)
            ->load(Area::PART_CONFIG);
        $objectManager->get(State::class)
            ->setAreaCode(FrontNameResolver::AREA_CODE);

        $this->templateFactoryMock = $this->getMockBuilder(TemplateInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->templateModel = $objectManager->create(Template::class);
        $this->templateModel->load(self::TEMPLATE_CODE, 'template_code');
        $this->templateFactoryMock->expects($this->once())
            ->method("create")
            ->willReturn($this->templateModel);
        $this->model = $objectManager->create(
            Validator::class,
            ['templateFactory' => $this->templateFactoryMock]
        );
    }
}
