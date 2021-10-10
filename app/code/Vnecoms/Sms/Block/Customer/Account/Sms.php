<?php
namespace Vnecoms\Sms\Block\Customer\Account;

class Sms extends \Magento\Customer\Block\Form\Edit
{
    /**
     * Get Post Action Url
     * 
     * @return string
     */
    public function getPostUrl(){
        return $this->getUrl('customer/account/smsPost');
    }
}
