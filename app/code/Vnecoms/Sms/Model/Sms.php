<?php
namespace Vnecoms\Sms\Model;

class Sms extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_FAILED         = 0;
    const STATUS_PENDING        = 1;
    const STATUS_SENT           = 2;
    const STATUS_DELIVERED      = 3;
    const STATUS_UNDELIVERED    = 4;
    const STATUS_NOT_ENOUGH_CREDIT  = 5;

    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'vsms';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'sms';
    
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Sms\Model\ResourceModel\Sms');
    }
    
    /**
     * Get helper
     * 
     * @return \Vnecoms\Sms\Helper\Data
     */
    public function getHelper(){
        if(!$this->helper){
            $this->helper = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Vnecoms\Sms\Helper\Data');
        }
        
        return $this->helper;
    }
    
    /**
     * Get message detail from sms gateway
     */
    public function getMessageDetailFromGateway(){
        $helper = $this->getHelper();
        $gateway = $helper->getGatewayModel($this->getGateway());
        return $gateway->getSms($this->getSid());
    }
}
