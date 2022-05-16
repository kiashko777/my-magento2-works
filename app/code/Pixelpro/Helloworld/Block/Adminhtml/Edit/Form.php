<?php

namespace Pixelpro\Helloworld\Block\Adminhtml\Edit;
use Ashsmith\Blog\Model\Post;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Adminhtml blog post edit form
 */
class Form extends Generic
{
    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry             $registry,
        FormFactory     $formFactory,
        Store       $systemStore,
        array                                   $data = []
    )
    {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('post_form');
        //$this->setTitle(__('Datos del post'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var Post $model */
        //$model = $this->_coreRegistry->registry('blog_post');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setHtmlIdPrefix('post_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Post Data'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'title',
            'text',
            ['name' => 'title', 'label' => __('Title'), 'titulo' => __('Title'), 'required' => true]
        );

        $fieldset->addField(
            'mime_content_type(filename)',
            'editor',
            [
                'name' => 'content',
                'label' => __('Content'),
                'title' => __('Content'),
                'style' => 'height:36em',
                'required' => true
            ]
        );
        //$form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

