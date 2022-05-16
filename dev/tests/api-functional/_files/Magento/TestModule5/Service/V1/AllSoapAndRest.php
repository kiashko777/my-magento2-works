<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule5\Service\V1;

use Magento\TestModule5\Service\V1\Entity\AllSoapAndRestFactory;

class AllSoapAndRest implements AllSoapAndRestInterface
{
    /**
     * @var AllSoapAndRestFactory
     */
    protected $factory;

    /**
     * @param AllSoapAndRestFactory $factory
     */
    public function __construct(AllSoapAndRestFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function item($entityId)
    {
        return $this->factory->create()
            ->setEntityId($entityId)
            ->setName('testItemName')
            ->setIsEnabled(true)
            ->setHasOrders(true);
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        $allSoapAndRest1 = $this->factory->create()->setEntityId(1)->setName('testProduct1');
        $allSoapAndRest2 = $this->factory->create()->setEntityId(2)->setName('testProduct2');
        return [$allSoapAndRest1, $allSoapAndRest2];
    }

    /**
     * {@inheritdoc}
     */
    public function create(Entity\AllSoapAndRest $item)
    {
        return $this->factory->create()
            ->setEntityId($item->getEntityId())
            ->setName($item->getName())
            ->setIsEnabled($item->isEnabled())
            ->setHasOrders($item->hasOrders());
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity\AllSoapAndRest $entityItem)
    {
        return $entityItem;
    }

    /**
     * {@inheritdoc}
     */
    public function nestedUpdate(
        $parentId,
        $entityId,
        Entity\AllSoapAndRest $entityItem
    )
    {
        return $entityItem;
    }
}
