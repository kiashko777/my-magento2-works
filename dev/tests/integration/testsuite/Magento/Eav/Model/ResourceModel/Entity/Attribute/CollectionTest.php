<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Eav\Model\ResourceModel\Entity\Attribute;

use Magento\Framework\DB\Select;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use ReflectionObject;

class CollectionTest extends TestCase
{
    /**
     * @var Collection
     */
    protected $_model;

    public function testSetAttributeGroupFilter()
    {
        $collection = Bootstrap::getObjectManager()->create(
            Collection::class
        );
        $groupsPresent = $this->_getGroups($collection);
        $includeGroupId = current($groupsPresent);

        $this->_model->setAttributeGroupFilter($includeGroupId);
        $groups = $this->_getGroups($this->_model);

        $this->assertEquals([$includeGroupId], $groups);
    }

    /**
     * Returns array of group ids, present in collection attributes
     *
     * @param Collection $collection
     * @return array
     */
    protected function _getGroups($collection)
    {
        $collection->addSetInfo();

        $groups = [];
        foreach ($collection as $attribute) {
            foreach ($attribute->getAttributeSetInfo() as $setInfo) {
                $groupId = $setInfo['group_id'];
                $groups[$groupId] = $groupId;
            }
        }
        return array_values($groups);
    }

    /**
     * Test if getAllIds method return results after using setInAllAttributeSetsFilter method
     *
     * @covers \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection::setInAllAttributeSetsFilter()
     * @covers \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection::getAllIds()
     */
    public function testSetInAllAttributeSetsFilterWithGetAllIds()
    {
        $sets = [1];
        $this->_model->setInAllAttributeSetsFilter($sets);
        $attributeIds = $this->_model->getAllIds();
        $this->assertGreaterThan(0, count($attributeIds));
    }

    public function testAddAttributeGrouping()
    {
        $select = $this->_model->getSelect();
        $this->assertEmpty($select->getPart(Select::GROUP));
        $this->_model->addAttributeGrouping();
        $this->assertEquals(['main_table.attribute_id'], $select->getPart(Select::GROUP));
    }

    protected function setUp(): void
    {
        $this->_model = Bootstrap::getObjectManager()->create(
            Collection::class
        );
    }

    /**
     * Returns array of set ids, present in collection attributes
     *
     * @param Collection $collection
     * @return array
     */
    protected function _getSets($collection)
    {
        $collection->addSetInfo();

        $sets = [];
        foreach ($collection as $attribute) {
            foreach (array_keys($attribute->getAttributeSetInfo()) as $setId) {
                $sets[$setId] = $setId;
            }
        }
        return array_values($sets);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $reflection = new ReflectionObject($this);
        foreach ($reflection->getProperties() as $property) {
            if (!$property->isStatic() && 0 !== strpos($property->getDeclaringClass()->getName(), 'PHPUnit')) {
                $property->setAccessible(true);
                $property->setValue($this, null);
            }
        }
    }
}
