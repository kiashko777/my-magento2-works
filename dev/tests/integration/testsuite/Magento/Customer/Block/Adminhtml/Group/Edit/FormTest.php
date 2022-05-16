<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Adminhtml\Group\Edit;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\Tax\Model\TaxClass\Source\Customer;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Magento\Customer\Block\Adminhtml\Group\Edit\Form
 *
 * @magentoAppArea Adminhtml
 */
class FormTest extends TestCase
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * Test retrieving a valid group form.
     */
    public function testGetForm()
    {
        $this->registry
            ->register(RegistryConstants::CURRENT_GROUP_ID, $this->groupManagement->getDefaultGroup(0)->getId());

        /** @var $block Form */
        $block = $this->layout->createBlock(Form::class, 'block');
        $form = $block->getForm();

        $this->assertEquals('edit_form', $form->getId());
        $this->assertEquals('post', $form->getMethod());
        $baseFieldSet = $form->getElement('base_fieldset');
        $this->assertNotNull($baseFieldSet);
        $groupCodeElement = $form->getElement('customer_group_code');
        $this->assertNotNull($groupCodeElement);
        $taxClassIdElement = $form->getElement('tax_class_id');
        $this->assertNotNull($taxClassIdElement);
        $idElement = $form->getElement('id');
        $this->assertNotNull($idElement);
        $this->assertEquals('1', $idElement->getValue());
        $this->assertEquals('3', $taxClassIdElement->getValue());
        /** @var Customer $taxClassCustomer */
        $taxClassCustomer = Bootstrap::getObjectManager()->get(Customer::class);
        $this->assertEquals($taxClassCustomer->toOptionArray(false), $taxClassIdElement->getData('values'));
        $this->assertEquals('General', $groupCodeElement->getValue());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testGetFormExistInCustomGroup()
    {
        $builder = Bootstrap::getObjectManager()->create(FilterBuilder::class);
        /** @var SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = Bootstrap::getObjectManager()
            ->create(SearchCriteriaBuilder::class)
            ->addFilters([$builder->setField('code')->setValue('custom_group')->create()]);
        /** @var GroupInterface $customerGroup */
        $customerGroup = $this->groupRepository->getList($searchCriteria->create())->getItems()[0];
        $this->registry->register(RegistryConstants::CURRENT_GROUP_ID, $customerGroup->getId());

        /** @var $block Form */
        $block = $this->layout->createBlock(Form::class, 'block');
        $form = $block->getForm();

        $this->assertEquals('edit_form', $form->getId());
        $this->assertEquals('post', $form->getMethod());
        $baseFieldSet = $form->getElement('base_fieldset');
        $this->assertNotNull($baseFieldSet);
        $groupCodeElement = $form->getElement('customer_group_code');
        $this->assertNotNull($groupCodeElement);
        $taxClassIdElement = $form->getElement('tax_class_id');
        $this->assertNotNull($taxClassIdElement);
        $idElement = $form->getElement('id');
        $this->assertNotNull($idElement);
        $this->assertEquals($customerGroup->getId(), $idElement->getValue());
        $this->assertEquals($customerGroup->getTaxClassId(), $taxClassIdElement->getValue());
        /** @var Customer $taxClassCustomer */
        $taxClassCustomer = Bootstrap::getObjectManager()->get(Customer::class);
        $this->assertEquals($taxClassCustomer->toOptionArray(false), $taxClassIdElement->getData('values'));
        $this->assertEquals($customerGroup->getCode(), $groupCodeElement->getValue());
    }

    /**
     * Execute per test initialization.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->layout = Bootstrap::getObjectManager()->create(Layout::class);
        $this->groupRepository = Bootstrap::getObjectManager()
            ->get(GroupRepositoryInterface::class);
        $this->groupManagement = Bootstrap::getObjectManager()
            ->get(GroupManagementInterface::class);
        $this->registry = Bootstrap::getObjectManager()->get(Registry::class);
    }

    /**
     * Execute per test cleanup.
     */
    protected function tearDown(): void
    {
        $this->registry->unregister(RegistryConstants::CURRENT_GROUP_ID);
    }
}
