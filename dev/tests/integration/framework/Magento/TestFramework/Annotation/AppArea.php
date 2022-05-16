<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Annotation;

use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\TestFramework\Application;
use PHPUnit\Framework\TestCase;

class AppArea
{
    const ANNOTATION_NAME = 'magentoAppArea';

    /**
     * @var Application
     */
    private $_application;

    /**
     * List of allowed areas.
     *
     * @var array
     */
    private $_allowedAreas = [
        Area::AREA_GLOBAL,
        Area::AREA_ADMINHTML,
        Area::AREA_FRONTEND,
        Area::AREA_WEBAPI_REST,
        Area::AREA_WEBAPI_SOAP,
        Area::AREA_CRONTAB,
        Area::AREA_GRAPHQL
    ];

    /**
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->_application = $application;
    }

    /**
     * Start test case event observer
     *
     * @param TestCase $test
     */
    public function startTest(TestCase $test)
    {
        $area = $this->_getTestAppArea($test->getAnnotations());
        if ($this->_application->getArea() !== $area) {
            $this->_application->reinitialize();

            if ($this->_application->getArea() !== $area) {
                $this->_application->loadArea($area);
            }
        }
    }

    /**
     * Get current application area
     *
     * @param array $annotations
     * @return string
     * @throws LocalizedException
     */
    protected function _getTestAppArea($annotations)
    {
        $area = isset(
            $annotations['method'][self::ANNOTATION_NAME]
        ) ? current(
            $annotations['method'][self::ANNOTATION_NAME]
        ) : (isset(
            $annotations['class'][self::ANNOTATION_NAME]
        ) ? current(
            $annotations['class'][self::ANNOTATION_NAME]
        ) : Application::DEFAULT_APP_AREA);

        if (false == in_array($area, $this->_allowedAreas)) {
            throw new LocalizedException(
                __(
                    'Invalid "@magentoAppArea" annotation, can be "%1" only.',
                    implode('", "', $this->_allowedAreas)
                )
            );
        }

        return $area;
    }
}
