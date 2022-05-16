<?php
/**
 * List of blocks to be skipped from instantiation test
 *
 * Format: array('Block_Class_Name', ...)
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Config\Block\System\Config\Edit;
use Magento\Config\Block\System\Config\Tabs;
use Magento\Email\Block\Adminhtml\Template;
use Magento\LayeredNavigation\Block\Navigation;
use Magento\LayeredNavigation\Block\Navigation\State;
use Magento\Paypal\Block\Express\InContext\Minicart\Button;
use Magento\Reminder\Block\Adminhtml\Reminder\Edit\Tab\Customers;
use Magento\Review\Block\Form;

return [
    // Blocks with abstract constructor arguments
    Template::class,
    \Magento\Email\Block\Adminhtml\Template\Edit::class,
    Edit::class,
    \Magento\Config\Block\System\Config\Form::class,
    Tabs::class,
    Form::class,
    // Fails because of dependence on registry
    Customers::class,
    Navigation::class,
    State::class,
    Button::class,
];
