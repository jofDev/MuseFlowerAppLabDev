<?php
namespace Vnecoms\Core\Controller\Adminhtml\Licenses;

use Vnecoms\Core\Controller\Adminhtml\Action;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class NewAction extends Action implements HttpGetActionInterface
{
    /**
     * @return void
     */
    public function execute()
    {
        $model = $this->_objectManager->create('Vnecoms\Core\Model\Key');

        $this->_coreRegistry->register('current_license', $model);
        $this->_coreRegistry->register('license', $model);

        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('License Info'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend( __('New License'));


        $breadcrumb = __('New License');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
}
