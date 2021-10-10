<?php

namespace Applab\EventsPro\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Delete extends Action
{

    protected $eventsProData;

    public function __construct(
        Context $context,
        \Applab\EventsPro\Model\EventsProData $eventsProData
    ) {
        parent::__construct($context);
        $this->eventsProData = $eventsProData;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Applab_EventsPro::index_delete');
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $events = $this->eventsProData;
                $events->load($id);
                $events->delete();
                $this->messageManager->addSuccess(__('Event deleted successfully.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addError(__('Event does not exist.'));
        return $resultRedirect->setPath('*/*/');
    }
}