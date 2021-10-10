<?php
namespace Vnecoms\Sms\Model;

class Rule extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Prefix of model events names
     * @var string
     */
    protected $_eventPrefix = 'vsms_rule';
    
    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'rule';
    
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Sms\Model\ResourceModel\Rule');
    }
    
    /**
     * Validate mobile number
     * 
     * @param string $number
     * @return boolean
     */
    public function isBlocked($number){
        $result = @preg_match($this->getRule(), $number);
        if($result === false){
            $numbers = str_replace(' ', '', $this->getRule());
            $numbers = explode(",", $numbers);
            $result = in_array($number, $numbers);
        }
        
        return $result;
    }
}
