<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\PersistentHistory\Model\Observer;
use Magento\Sales\Block\Reorder\Sidebar;

return [
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
    ]
];
