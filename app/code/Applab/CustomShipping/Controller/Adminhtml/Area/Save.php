<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Applab\CustomShipping\Controller\Adminhtml\Area;

/**
 * Description of Save
 *
 * @author dharmendra
 */
class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $_date;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date)
    {
        parent::__construct($context);
        $this->_date = $date;
    }
    
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Applab\CustomShipping\Model\Area');

            $id = $this->getRequest()->getParam('area_id');
            if ($id) {
                $model->load($id);
            }            
            
            $model->setData($data);
            
            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the Area.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['area_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Area.'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['area_id' => $this->getRequest()->getParam('area_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}