<?php
namespace Vnecoms\Sms\Block\Customer;

class Login extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Sms\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
    }
    
    
    protected function _prepareLayout(){
        parent::_prepareLayout();
        if($this->helper->isEnableMobileLogin() && $this->helper->getCurrentGateway()){
            $this->getParentBlock()->setTemplate('Vnecoms_Sms::customer/login.phtml');
        }
        return $this;
    }
}
