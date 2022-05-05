<?php
declare(strict_types=1);

namespace Devall\Customform\Controller\Adminhtml\Customform;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;

class Delete extends \Devall\Customform\Controller\Adminhtml\Customform
{

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Devall\Customform\Model\Customform::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Customform!'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Customform to delete!'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
