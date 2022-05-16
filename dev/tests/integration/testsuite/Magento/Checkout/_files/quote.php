<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Quote\Model\Quote;
use Magento\TestFramework\Helper\Bootstrap;

$quote = Bootstrap::getObjectManager()->create(Quote::class);
$quote->setData(['store_id' => 1, 'is_active' => 0, 'is_multi_shipping' => 0]);
$quote->save();
