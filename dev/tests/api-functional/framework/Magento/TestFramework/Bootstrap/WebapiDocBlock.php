<?php
/**
 * Bootstrap of the custom Web API DocBlock annotations.
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Bootstrap;

use Magento\TestFramework\Annotation\ApiConfigFixture;
use Magento\TestFramework\Annotation\ApiDataFixture;
use Magento\TestFramework\Annotation\ConfigFixture;
use Magento\TestFramework\Application;

/**
 * @inheritdoc
 */
class WebapiDocBlock extends DocBlock
{
    /**
     * Get list of subscribers.
     *
     * In addition, register magentoApiDataFixture and magentoConfigFixture
     * annotation processors
     *
     * @param Application $application
     * @return array
     */
    protected function _getSubscribers(Application $application)
    {
        $subscribers = parent::_getSubscribers($application);
        foreach ($subscribers as $key => $subscriber) {
            if (get_class($subscriber) == ConfigFixture::class) {
                unset($subscribers[$key]);
            }
        }
        $subscribers[] = new ApiDataFixture();
        $subscribers[] = new ApiConfigFixture();

        return $subscribers;
    }
}
