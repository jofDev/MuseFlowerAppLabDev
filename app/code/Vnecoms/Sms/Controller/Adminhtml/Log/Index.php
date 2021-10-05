<?php
namespace Vnecoms\Sms\Controller\Adminhtml\Log;

use Vnecoms\Sms\Controller\Adminhtml\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Index extends Action implements HttpGetActionInterface
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return parent::_isAllowed() && $this->_authorization->isAllowed('Vnecoms_Sms::log');
    }
    

    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Sms Nofitication'), __('Sms Nofitication'))->_addBreadcrumb(__('Log'), __('Log'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Log'));
        $this->_view->renderLayout();
    }
}
