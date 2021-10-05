<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Applab\CustomShipping\Controller\Adminhtml\Area;

/**
 * Description of Edit
 *
 * @author dharmendra
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry)
    {
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    public function execute() {

        $id = $this->getRequest()->getParam('area_id');
        $model = $this->_objectManager->create('Applab\CustomShipping\Model\Area');
        
        if($id){
            $model->load($id);
            if(!$model->getId()){
                $this->messageManager->addError(__('This Area no longer exits. '));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
    	}
        
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        
    
        $this->_coreRegistry->register('customshipping_area', $model);
        
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Area') : __('New Area'),
            $id ? __('Edit Area') : __('New Area')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Area'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getname() : __('New Area'));
               
        return $resultPage;
    }
    
    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Applab_CustomShipping::area')
            ->addBreadcrumb(__('Area'), __('Area'))
            ->addBreadcrumb(__('Manage Area'), __('Manage Area'));
        return $resultPage;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Applab_CustomShipping::area_edit');
    }
}