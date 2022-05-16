<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\Error\ProcessorFactory;

require_once 'processorFactory.php';

$processorFactory = new ProcessorFactory();
$processor = $processorFactory->createProcessor();
$response = $processor->process503();
$response->sendResponse();
