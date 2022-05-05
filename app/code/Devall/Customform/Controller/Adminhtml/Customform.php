<?php
declare(strict_types=1);

namespace Devall\Customform\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;

abstract class Customform extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Devall_Customform::top_level';
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context  $context,
        Registry $coreRegistry
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Devall'), __('Devall'))
            ->addBreadcrumb(__('Customform'), __('Customform'));
        return $resultPage;
    }
}
