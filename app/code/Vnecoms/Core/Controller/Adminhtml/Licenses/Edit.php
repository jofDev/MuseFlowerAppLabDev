<?php
namespace Vnecoms\Core\Controller\Adminhtml\Licenses;

use Vnecoms\Core\Controller\Adminhtml\Action;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Edit extends Action implements HttpGetActionInterface
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Vnecoms\Core\Model\Key');
        if ($id) {
            $model->load($id);
            if (!$model->getKeyId()) {
                $this->messageManager->addError(__('This license no longer exists.'));
                $this->_redirect('vnecoms/*');
                return;
            }
        }

        $this->_coreRegistry->register('current_license', $model);
        $this->_coreRegistry->register('license', $model);

        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('License Info'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getKeyId() ? $model->getLicenseKey() : __('New License')
        );


        $breadcrumb = $id ? __('View License') : __('New License');
        $this->_addBreadcrumb($breadcrumb, $breadcrumb);
        $this->_view->renderLayout();
    }
}
