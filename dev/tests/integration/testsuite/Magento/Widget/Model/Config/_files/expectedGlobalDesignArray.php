<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Block\Adminhtml\Product\Widget\Chooser;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Sales\Block\Widget\Guest\Form;

return [
    'sales_widget_guestform' => [
        '@' => ['type' => Form::class],
        'is_email_compatible' => '1',
        'name' => 'Orders and Returns',
        'description' => 'Orders and Returns Search Form',
        'parameters' => [
            'title' => ['type' => 'text', 'visible' => '0', 'label' => 'Anchor Custom Title'],
            'template' => [
                'type' => 'select',
                'value' => 'hierarchy/widget/link/link_block.phtml',
                'values' => [
                    'default' => [
                        'value' => 'hierarchy/widget/link/link_block.phtml',
                        'label' => 'CMS Page link Block Template',
                    ],
                    'link_inline' => [
                        'value' => 'hierarchy/widget/link/link_inline.phtml',
                        'label' => 'CMS Page link Inline Template',
                    ],
                ],
                'visible' => '0',
            ],
            'link_display' => [
                'source_model' => Yesno::class,
                'type' => 'select',
                'visible' => '1',
                'sort_order' => '10',
                'label' => 'Display a link to Loading a Spreadsheet',
                'description' => "Defines whether a link to My Account",
            ],
            'link_text' => [
                'type' => 'text',
                'value' => 'Load a list of SKUs',
                'visible' => '1',
                'required' => '1',
                'sort_order' => '20',
                'label' => 'link Text',
                'description' => 'The text of the link to the My Account &gt; Order by SKU page',
                'depends' => ['link_display' => ['value' => '1']],
            ],
            'id_path' => [
                'type' => 'label',
                '@' => ['type' => 'complex'],
                'helper_block' => [
                    'type' => Chooser::class,
                    'data' => ['button' => ['open' => 'Select Products...']],
                ],
                'visible' => '1',
                'required' => '1',
                'sort_order' => '10',
                'label' => 'Products',
            ],
        ],
        'supported_containers' => [
            '0' => ['container_name' => 'left', 'template' => ['default' => 'default_template']],
            '1' => ['container_name' => 'right', 'template' => ['default' => 'default_template']],
        ],
    ],
    'new_products' => [
        '@' => ['type' => Form::class],
        'is_email_compatible' => '1',
        'name' => 'Orders and Returns',
        'description' => 'Orders and Returns Search Form',
        'parameters' => [
            'title' => ['type' => 'text', 'visible' => '0', 'label' => 'Anchor Custom Title'],
            'template' => [
                'type' => 'select',
                'values' => [
                    'default_template' => ['value' => 'widget/guest/form.phtml', 'label' => 'Default Template'],
                ],
                'visible' => '0',
            ],
        ],
        'supported_containers' => [
            ['container_name' => 'left', 'template' => ['default' => 'default_template']],
            ['container_name' => 'right', 'template' => ['default' => 'default_template']],
        ],
    ]
];
