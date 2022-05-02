<?php

namespace Alliance\Linkedin\Model\Plugin\Checkout;
class LayoutProcessor
{
    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array                                            $jsLayout
    )
    {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['custom_field'] = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
                'options' => [],
                'id' => 'linkedin_profile'
            ],
            'dataScope' => 'shippingAddress.custom_attributes.custom_field',
            'label' => 'LinkedIn Profile',
            'provider' => 'checkoutProvider',
            'visible' => true,
            'required' => true,
            'validation' => [],
            'sortOrder' => 250,
            'id' => 'linkedin_profile'
        ];
        return $jsLayout;
    }
}
