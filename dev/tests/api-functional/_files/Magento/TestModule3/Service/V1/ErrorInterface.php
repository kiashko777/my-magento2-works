<?php
/**
 * Interface for a test service for error handling testing
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestModule3\Service\V1;

use Magento\TestModule3\Service\V1\Entity\Parameter;
use Magento\TestModule3\Service\V1\Entity\WrappedErrorParameter;

interface ErrorInterface
{
    /**
     * @return Parameter
     */
    public function success();

    /**
     * @return int Status
     */
    public function resourceNotFoundException();

    /**
     * @return int Status
     */
    public function serviceException();

    /**
     * @return int Status
     */
    public function authorizationException();

    /**
     * @return int Status
     */
    public function webapiException();

    /**
     * @return int Status
     */
    public function otherException();

    /**
     * @return int Status
     */
    public function returnIncompatibleDataType();

    /**
     * @param WrappedErrorParameter[] $wrappedErrorParameters
     * @return int Status
     */
    public function inputException($wrappedErrorParameters);
}
