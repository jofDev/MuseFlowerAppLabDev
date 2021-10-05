<?php
namespace Vnecoms\Sms\Block\Customer\Account;

class Mobile extends \Vnecoms\Sms\Block\Customer\Register\Mobile
{
    /**
     * @return boolean
     */
    public function isEnabledVerifying(){
        return $this->smsHelper->isEnableVerifyingCustomerMobile();
    }
    

    /**
     * Get current Mobile Number
     * 
     * @return string
     */
    public function getInitMobileNumber(){
        return $this->customerSession->getCustomer()->getMobilenumber();
    }
    
    /**
     * Is verified mobile
     * 
     * @return boolean
     */
    public function getIsVerifiedMobile(){
        return (bool)$this->customerSession->getCustomer()->getMobilenumber();
    }
}
