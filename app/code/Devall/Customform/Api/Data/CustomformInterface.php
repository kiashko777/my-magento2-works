<?php
declare(strict_types=1);

namespace Devall\Customform\Api\Data;

interface CustomformInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ID = 'id';
    const NAME = 'name';
    const CREATED_AT = 'created_at';
    const DATEPICKER = 'datepicker';
    const STATUS = 'status';

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Devall\Customform\Api\Data\CustomformInterface
     */
    public function setId($id);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Devall\Customform\Api\Data\CustomformExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Devall\Customform\Api\Data\CustomformExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Devall\Customform\Api\Data\CustomformExtensionInterface $extensionAttributes
    );

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Devall\Customform\Api\Data\CustomformInterface
     */
    public function setName($name);


    /**
     * Get datepicker
     * @return string|null
     */
    public function getDatepicker();

    /**
     * Set datepicker
     * @param string $datepicker
     * @return \Devall\Customform\Api\Data\CustomformInterface
     */
    public function setDatepicker($datepicker);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Devall\Customform\Api\Data\CustomformInterface
     */
    public function setStatus($status);

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created_at
     * @param string $createdAt
     * @return \Devall\Customform\Api\Data\CustomformInterface
     */
    public function setCreatedAt($createdAt);
}
