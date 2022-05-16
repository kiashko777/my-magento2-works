<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tax\Controller\Adminhtml;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data;
use Magento\Tax\Api\Data\TaxClassInterfaceFactory;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Magento\Tax\Model\ClassModel;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea Adminhtml
 */
class TaxTest extends AbstractBackendController
{
    /**
     * @dataProvider ajaxActionDataProvider
     * @magentoDbIsolation enabled
     *
     * @param array $postData
     * @param array $expectedData
     */
    public function testAjaxSaveAction($postData, $expectedData)
    {
        $this->getRequest()->setPostValue($postData);

        $this->dispatch('backend/tax/tax/ajaxSave');

        $jsonBody = $this->getResponse()->getBody();
        $result = Bootstrap::getObjectManager()->get(
            Data::class
        )->jsonDecode(
            $jsonBody
        );

        $this->assertArrayHasKey('class_id', $result);

        $classId = $result['class_id'];
        /** @var $class ClassModel */
        $class = Bootstrap::getObjectManager()->create(
            ClassModel::class
        )->load($classId, 'class_id');
        $this->assertEquals($expectedData['class_name'], $class->getClassName());
    }

    /**
     * @dataProvider ajaxActionDataProvider
     * @magentoDbIsolation enabled
     *
     * @param array $taxClassData
     */
    public function testAjaxDeleteAction($taxClassData)
    {
        /** @var TaxClassRepositoryInterface $taxClassService */
        $taxClassService = Bootstrap::getObjectManager()->get(
            TaxClassRepositoryInterface::class
        );

        $taxClassFactory = Bootstrap::getObjectManager()->get(
            TaxClassInterfaceFactory::class
        );
        $taxClass = $taxClassFactory->create();
        $taxClass->setClassName($taxClassData['class_name'])
            ->setClassType($taxClassData['class_type']);

        $taxClassId = $taxClassService->save($taxClass);

        /** @var $class ClassModel */
        $class = Bootstrap::getObjectManager()->create(
            ClassModel::class
        )->load($taxClassId, 'class_id');
        $this->assertEquals($taxClassData['class_name'], $class->getClassName());
        $this->assertEquals($taxClassData['class_type'], $class->getClassType());

        $postData = ['class_id' => $taxClassId];
        $this->getRequest()->setPostValue($postData);
        $this->dispatch('backend/tax/tax/ajaxDelete');

        $isFound = true;
        try {
            $taxClassId = $taxClassService->get($taxClassId);
        } catch (NoSuchEntityException $e) {
            $isFound = false;
        }
        $this->assertFalse($isFound, "Tax Class was found when it should have been deleted.");
    }

    /**
     * @return array
     */
    public function ajaxActionDataProvider()
    {
        return [
            [
                ['class_type' => 'CUSTOMER', 'class_name' => 'Class Name'],
                ['class_name' => 'Class Name'],
            ],
            [
                ['class_type' => 'PRODUCT', 'class_name' => '11111<22222'],
                ['class_name' => '11111<22222']
            ],
            [
                ['class_type' => 'CUSTOMER', 'class_name' => '   12<>sa&df    '],
                ['class_name' => '12<>sa&df']
            ]
        ];
    }
}
