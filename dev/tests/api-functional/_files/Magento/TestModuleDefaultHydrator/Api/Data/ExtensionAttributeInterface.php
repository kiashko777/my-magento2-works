<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModuleDefaultHydrator\Api\Data;

interface ExtensionAttributeInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const CUSTOMER_ID = 'customer_id';
    const VALUE = 'value';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     * @api
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     * @api
     */
    public function setId($id);

    /**
     * Get customer ID
     *
     * @return int|null
     * @api
     */
    public function getCustomerId();

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return $this
     * @api
     */
    public function setCustomerId($customerId);

    /**
     * Get value
     *
     * @return string|null
     * @api
     */
    public function getValue();

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     * @api
     */
    public function setValue($value);
}
