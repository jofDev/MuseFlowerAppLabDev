<?php
namespace Vnecoms\SmsOoredoo\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_USER                 = 'vsms/settings/ooredoo_user';
    const XML_PATH_PASS                 = 'vsms/settings/ooredoo_password';
    const XML_PATH_CUSTOMER_ID          = 'vsms/settings/ooredoo_customer_id';
    const XML_PATH_SENDER               = 'vsms/settings/ooredoo_sender';
    const XML_PATH_MESSAGE_TYPE         = 'vsms/settings/ooredoo_message_type';
    
    /**
     * @return string
     */
    public function getUser(){
        return $this->scopeConfig->getValue(self::XML_PATH_USER);
    }
    
    /**
     * @return string
     */
    public function getPassword(){
        return $this->scopeConfig->getValue(self::XML_PATH_PASS);
    }
    
    /**
     * @return string
     */
    public function getCustomerId(){
        return $this->scopeConfig->getValue(self::XML_PATH_CUSTOMER_ID);
    }
    
    /**
     * @return string
     */
    public function getSender(){
        return $this->scopeConfig->getValue(self::XML_PATH_SENDER);
    }
    
    /**
     * @return string
     */
    public function getMessageType(){
        return $this->scopeConfig->getValue(self::XML_PATH_MESSAGE_TYPE);
    }
}
