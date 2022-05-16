<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Block\Adminhtml\Group;

use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Magento\Customer\Block\Adminhtml\Group\Edit
 *
 * @magentoAppArea Adminhtml
 */
class EditTest extends AbstractController
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
     * Verify that the Delete button does not exist for the default group.
     * @magentoAppIsolation enabled
     */
    public function testDeleteButtonNotExistInDefaultGroup()
    {
        $groupId = $this->groupManagement->getDefaultGroup(0)->getId();
        $this->registry->register(RegistryConstants::CURRENT_GROUP_ID, $groupId);
        $this->getRequest()->setParam('id', $groupId);

        /** @var $block Edit */
        $block = $this->layout->createBlock(Edit::class, 'block');
        $buttonsHtml = $block->getButtonsHtml();

        $this->assertStringNotContainsString('delete', $buttonsHtml);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testDeleteButtonExistInCustomGroup()
    {
        $builder = Bootstrap::getObjectManager()->create(FilterBuilder::class);
        /** @var SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = Bootstrap::getObjectManager()
            ->create(SearchCriteriaBuilder::class)
            ->addFilters([$builder->setField('code')->setValue('custom_group')->create()])->create();
        $customerGroup = $this->groupRepository->getList($searchCriteria)->getItems()[0];
        $this->getRequest()->setParam('id', $customerGroup->getId());
        $this->registry->register(RegistryConstants::CURRENT_GROUP_ID, $customerGroup->getId());

        /** @var $block Edit */
        $block = $this->layout->createBlock(Edit::class, 'block');
        $buttonsHtml = $block->getButtonsHtml();

        $this->assertStringContainsString('delete', $buttonsHtml);
    }

    /**
     * Execute per test initialization.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        $this->groupRepository = Bootstrap::getObjectManager()->create(
            GroupRepositoryInterface::class
        );
        $this->groupManagement = Bootstrap::getObjectManager()->create(
            GroupManagementInterface::class
        );
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
