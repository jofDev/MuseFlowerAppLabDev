<?php
namespace Vnecoms\Sms\Controller\Adminhtml\Blocklist;

use Vnecoms\Sms\Controller\Adminhtml\Blocklist\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Index extends Action implements HttpGetActionInterface
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Sms Nofitication'), __('Sms Nofitication'))->_addBreadcrumb(__('Block List'), __('Block List'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Block List'));
        $this->_view->renderLayout();
    }
}
