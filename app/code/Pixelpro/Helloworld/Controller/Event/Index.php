<?php

namespace Pixelpro\Helloworld\Controller\Event;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\DataObject;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected PageFactory $_pageFactory;

    public function __construct(
        Context      $context,
        PageFactory $pageFactory)
    {
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $message = new DataObject(array('message' => 'Pixelpro'));
        $this->_eventManager->dispatch('pixelpro_helloworld_show_message', ['message_text' => $message]);
        echo $message->getMessage();
        exit;
    }
}




