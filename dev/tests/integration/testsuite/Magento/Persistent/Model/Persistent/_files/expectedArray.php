<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\PersistentHistory\Model\Observer;
use Magento\Reports\Block\Product\Compared;
use Magento\Sales\Block\Reorder\Sidebar;

return [
    'blocks' => [
        'reorder_sidebar' => [
            'name_in_layout' => 'sale.reorder.sidebar',
            'class' => Observer::class,
            'method' => 'initReorderSidebar',
            'block_type' => Sidebar::class,
        ],
        'viewed_products' => [
            'name_in_layout' => 'left.reports.product.viewed',
            'class' => Observer::class,
            'method' => 'emulateViewedProductsBlock',
            'block_type' => Sidebar::class,
        ],
        'compared_products' => [
            'name_in_layout' => 'right.reports.product.compared',
            'class' => Observer::class,
            'method' => 'emulateComparedProductsBlock',
            'block_type' => Compared::class,
        ],
    ]
];
