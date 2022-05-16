<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework;

use Laminas\Stdlib\ParametersInterface;
use Magento\Framework\App\Request\Http;

/**
 * HTTP request implementation that is used instead core one for testing
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Request extends Http
{
    /**
     * Server super-global mock
     *
     * @var ParametersInterface
     */
    protected $_server;

    /**
     * Retrieve HTTP HOST.
     *
     * This method is a stub - all parameters are ignored, just static value returned.
     *
     * @param bool $trimPort
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getHttpHost($trimPort = true)
    {
        return $trimPort ? 'localhost' : 'localhost:81';
    }

    /**
     * Overridden getter to avoid using of $_SERVER
     *
     * @param string|null $name
     * @param mixed|null $default
     * @return ParametersInterface|array|mixed|null
     */
    public function getServer($name = null, $default = null)
    {
        if (null === $name) {
            return $this->_server;
        }
        return $this->_server->get($name, $default);
    }

    /**
     * Set "server" super-global mock
     *
     * @param ParametersInterface $server
     * @return Request
     */
    public function setServer(ParametersInterface $server)
    {
        $this->_server = $server;
        return $this;
    }
}
