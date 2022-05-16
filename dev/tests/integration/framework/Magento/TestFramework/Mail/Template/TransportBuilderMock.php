<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\Mail\Template;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Message;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\TestFramework\Mail\TransportInterfaceMock;

/**
 * Class TransportBuilderMock
 */
class TransportBuilderMock extends TransportBuilder
{
    /**
     * @var Message
     */
    protected $_sentMessage;

    /**
     * Return message object with prepared data
     *
     * @return Message|null
     */
    public function getSentMessage()
    {
        return $this->_sentMessage;
    }

    /**
     * Return transport mock.
     *
     * @return TransportInterfaceMock
     * @throws LocalizedException
     */
    public function getTransport()
    {
        $this->prepareMessage();
        $this->reset();
        return new TransportInterfaceMock($this->message);
    }

    /**
     * Reset object state
     *
     * @return $this
     */
    protected function reset()
    {
        $this->_sentMessage = $this->message;
        parent::reset();
    }
}
