<?php
namespace Vnecoms\Sms\Model;

use Magento\Framework\App\ObjectManager;
class Mobile extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_NOT_VERIFIED   = 0;
    const STATUS_VERIFIED       = 1;

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'vmobile';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'mobile';
    
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Sms\Model\ResourceModel\Mobile');
    }

    /**
     * @return boolean
     */
    public function isVerified(){
        return $this->getStatus() == self::STATUS_VERIFIED;
    }
    
    /**
     * @return boolean
     */
    public function isActive(){
        return $this->isVerified();
    }
    
    /**
     * Is expired OTP
     * 
     * @return boolean
     */
    public function isExpiredOTP(){
        $om = ObjectManager::getInstance();
        $helper = $om->get('Vnecoms\Sms\Helper\Data');
        $date = $om->get('Magento\Framework\Stdlib\DateTime\DateTime');
        return (strtotime($this->getOtpCreatedAt()) + $helper->getOtpExpiredPeriodTime()) < $date->timestamp();
    }
    
    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer(){
        if($this->customer === null){
            $this->customer = ObjectManager::getInstance()->create('Magento\Customer\Model\Customer');
            $this->customer->load($this->getCustomerId());
        }
        
        return $this->customer;
    }
}
