<?php
declare(strict_types=1);

namespace Devall\Customform\Model\Data;

use Devall\Customform\Api\Data\CustomformExtensionInterface;
use Devall\Customform\Api\Data\CustomformInterface;

class Customform extends \Magento\Framework\Api\AbstractExtensibleObject implements CustomformInterface
{
    /**
     * Get id
     * @return string|null
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Set id
     * @param string $id
     * @return CustomformInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return CustomformExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Devall\Customform\Api\Data\CustomformExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Devall\Customform\Api\Data\CustomformExtensionInterface $extensionAttributes
    )
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return CustomformInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return CustomformInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set created_at
     * @param string $createdAt
     * @return CustomformInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get datepicker
     * @return string|null
     */
    public function getDatepicker()
    {
        return $this->_get(self::DATEPICKER);
    }

    /**
     * Set datepicker
     * @param string $datepicker
     * @return CustomformInterface
     */
    public function setDatepicker($datepicker)
    {
        return $this->setData(self::DATEPICKER, $datepicker);
    }
}
