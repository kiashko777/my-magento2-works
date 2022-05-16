<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SomeModule\Model;

use Magento\SomeModule\ModelFactory;

/**
 * @SuppressWarnings(PHPMD.ConstructorWithNameAsEnclosingClass)
 */
class Test
{
    public function __construct()
    {
        new \Magento\SomeModule\Model\Element\Proxy();
    }

    /**
     * @param ModelFactory $factory
     * @param array $data
     */
    public function testModel(ModelFactory $factory, array $data = [])
    {
        $factory->create(BlockFactory::class, ['data' => $data]);
    }
}
