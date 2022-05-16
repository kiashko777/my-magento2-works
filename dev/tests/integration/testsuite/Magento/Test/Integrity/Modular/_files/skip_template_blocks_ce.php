<?php
/**
 * List of blocks to be skipped from template files test
 *
 * Format: array('Block_Class_Name', ...)
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\LayeredNavigation\Block\Navigation;
use Magento\LayeredNavigation\Block\Navigation\State;
use Magento\Paypal\Block\Express\InContext\Minicart\Button;
use Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Customers;

return [
    // Fails because of dependence on registry
    Customers::class,
    Navigation::class,
    State::class,
    Button::class,
];
