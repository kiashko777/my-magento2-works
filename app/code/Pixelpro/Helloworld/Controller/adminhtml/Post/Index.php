<?php

namespace Pixelpro\Helloworld\Controller\adminhtml\Post;
class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context        $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
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


