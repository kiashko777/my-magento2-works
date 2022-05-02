<?php

namespace Alliance\Linkedin\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    private $eavSetupFactory;

    private $eavConfig;

    private $attributeResource;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory              $eavSetupFactory,
        \Magento\Eav\Model\Config                       $eavConfig,
        \Magento\Customer\Model\ResourceModel\Attribute $attributeResource
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
        $this->attributeResource = $attributeResource;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->removeAttribute(Customer::ENTITY, "customer");

        $attributeSetId = $eavSetup->getDefaultAttributeSetId(Customer::ENTITY);
        $attributeGroupId = $eavSetup->getDefaultAttributeGroupId(Customer::ENTITY);

        $eavSetup->addAttribute(Customer::ENTITY, 'linkedin_profile', [
            'type' => 'varchar',
            'label' => 'LinkedIn Profile',
            'unique' => true,
            'input' => 'text',
            'required' => true,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 990,
            'position' => 990,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'visible_on_front' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => true,
            'system' => 0,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
        ]);

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'linkedin_profile');
        $attribute->setData('attribute_set_id', $attributeSetId);
        $attribute->setData('attribute_group_id', $attributeGroupId);


        $attribute->setData('used_in_forms', [
            'adminhtml_customer',
            'customer_account_create',
            'customer_account_edit',
            'adminhtml_checkout',
            'adminhtml_customer_address',
            'checkout_register',
            'customer_address_edit',
            'customer_register_address'
        ]);

        $this->attributeResource->save($attribute);
        $setup->endSetup();
    }
}


