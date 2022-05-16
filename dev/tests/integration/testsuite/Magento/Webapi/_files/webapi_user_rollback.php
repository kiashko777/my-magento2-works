<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User;

/** @var $model User */
$model = Bootstrap::getObjectManager()->create(User::class);
$userName = 'webapi_user';
$model->load($userName, 'username');
$model->delete();

/* Unlock account if it was locked */
/** @var RequestThrottler $throttler */
$throttler = Bootstrap::getObjectManager()->create(RequestThrottler::class);
$throttler->resetAuthenticationFailuresCount($userName, RequestThrottler::USER_TYPE_ADMIN);
