<?php
namespace Vnecoms\Sms\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\Sms\Helper\Data;

class VendorLoginPost implements ObserverInterface
{
    /**
     * Vendor Save After
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getRequest();

        if($request->getParam('login_type') == Data::LOGIN_TYPE_MOBILE){
            $login = $request->getParam('login');
            $login['username'] = $request->getParam('mobilenumber');
            $request->setPostValue('login', $login);
        }
    }
}
