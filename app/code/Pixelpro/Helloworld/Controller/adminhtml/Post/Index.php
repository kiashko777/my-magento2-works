<?php

namespace Pixelpro\Helloworld\Controller\adminhtml\Post;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory = false;

    public function __construct(
        Context        $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    //To check if user allowed to use the module

//    public function someControllerMethod()
//    {
//        return $this->_authorization->isAllowed('Pixelpro_Helloworld::post');
//    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Posts')));
        return $resultPage;
    }
}


