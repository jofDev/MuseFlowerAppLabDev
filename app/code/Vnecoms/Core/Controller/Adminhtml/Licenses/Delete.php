<?php
namespace Vnecoms\Core\Controller\Adminhtml\Licenses;

use Vnecoms\Core\Controller\Adminhtml\Action;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class Delete extends Action implements HttpPostActionInterface
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Vnecoms\Core\Model\Key');

        try{
            $model->load($id);
            if (!$id || !$model->getKeyId()) {
                throw new \Exception(__('This license no longer exists.'));
            }
            
            $model->delete();
            $this->messageManager->addSuccess(__("Your license has been deleted."));
        }catch (\Exception $e){
            $this->messageManager->addError($e->getMessage());
        }

        /*Everytime we delete the license, just clear the check lincense data*/
        $this->_auth->getAuthStorage()->setData('vnecoms_check_license_data',null);
        $this->_redirect('vnecoms/*');
    }
}
