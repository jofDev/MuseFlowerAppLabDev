<?php
namespace Applab\EventsPro\Controller\Adminhtml\Index;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Registrations extends Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    { 
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Applab_EventsPro::events_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Event Registrations'));
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return true;
    }
}