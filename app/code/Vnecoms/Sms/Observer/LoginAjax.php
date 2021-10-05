<?php
namespace Vnecoms\Sms\Observer;

use Magento\Framework\Event\ObserverInterface;
use Vnecoms\Sms\Helper\Data;

class LoginAjax implements ObserverInterface
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
        $credentials = json_decode($request->getContent(), true);
        if(isset($credentials['login_type']) && ($credentials['login_type'] == Data::LOGIN_TYPE_MOBILE)){
            $credentials['username'] = $credentials['mobilenumber'];
            $request->setContent(json_encode($credentials));
        }
    }
}
