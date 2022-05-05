<?php

namespace Devall\Customform\Controller\Index;

use Magento\Framework\App\Action\Context;
use Devall\Customform\Model\CustomformFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Customform
     */
    protected $_customform;
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;

    public function __construct(
        Context           $context,
        CustomformFactory $customform,
        UploaderFactory   $uploaderFactory,
        AdapterFactory    $adapterFactory,
        Filesystem        $filesystem
    )
    {
        $this->_customform = $customform;
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
//        $data = $this->getRequest()->getParams();
        $data = $this->validatedParams();

        $customform = $this->_customform->create();
        $customform->setData($data);
        if ($customform->save()) {
            $this->messageManager->addSuccessMessage(__('Congrats!You saved the data!'));
        } else {
            $this->messageManager->addErrorMessage(__('Sorry!Data was not saved!'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customform');
        return $resultRedirect;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('name')) === '') {
            throw new LocalizedException(__('Enter your Name and try again!'));
        }
        if (trim($request->getParam('datepicker')) === '') {
            throw new LocalizedException(__('Enter the Date and try again!'));
        }

        return $request->getParams();
    }
}
