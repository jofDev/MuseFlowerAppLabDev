<?php

namespace Applab\Brands\Controller\Adminhtml\Brand;

class Delete extends \Magento\Backend\App\Action
{
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Applab\Brands\Helper\Data $helperData
        ) {
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_Brand::brand_delete');
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    { 
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('brand_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create('Ves\Brand\Model\Brand');
                $model->load($id);
                $title = $model->getTitle();
            
                // update brands attribute
                $optionId = $model->getBrandMapId();
                $this->helperData->deleteOptionById($optionId);

                $model->delete();

                // display success message
                $this->messageManager->addSuccess(__('The brand has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['brand_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a brand to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }

}