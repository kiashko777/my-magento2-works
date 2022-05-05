<?php
declare(strict_types=1);

namespace Devall\Customform\Controller\Adminhtml\Customform;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context     $context,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magelearn::top_level');
        $resultPage->addBreadcrumb(__('Customform'), __('Customform'));
        $resultPage->addBreadcrumb(__('Manage Customform'), __('Manage Customform'));
        $resultPage->getConfig()->getTitle()->prepend(__("Manage Customform"));
        return $resultPage;
    }
}
