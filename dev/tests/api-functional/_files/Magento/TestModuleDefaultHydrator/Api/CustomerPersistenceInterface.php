<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModuleDefaultHydrator\Api;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;

/**
 * Customer CRUD interface
 */
interface CustomerPersistenceInterface
{
    /**
     * Create customer
     *
     * @param CustomerInterface $customer
     * @param string $passwordHash
     * @return CustomerInterface
     * @throws InputException If bad input is provided
     * @throws InputMismatchException If the provided email is already used
     * @throws LocalizedException
     * @api
     */
    public function save(CustomerInterface $customer);

    /**
     * Retrieve customer by email
     *
     * @param string $email
     * @param int|null $websiteId
     * @return CustomerInterface
     * @throws NoSuchEntityException If customer with the specified email does not exist
     * @throws LocalizedException
     * @api
     */
    public function get($email, $websiteId = null);

    /**
     * Retrieve customer by id
     *
     * @param int $id
     * @param int|null $websiteId
     * @return CustomerInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @api
     */
    public function getById($id, $websiteId = null);

    /**
     * Delete customer by id
     *
     * @param int $id
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @api
     */
    public function delete($id);
}
