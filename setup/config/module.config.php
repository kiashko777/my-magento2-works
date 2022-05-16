<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Laminas\ServiceManager\AbstractFactory\ReflectionBasedAbstractFactory;
use Magento\Setup\Controller\Index;

return [
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions' => false,
        'doctype' => 'HTML5',
        'template_path_stack' => [
            'setup' => __DIR__ . '/../view',
        ],
    ],
    'controllers' => [
        'factories' => [
            Index::class => ReflectionBasedAbstractFactory::class,
        ],
    ],
];
