<?php
namespace Vnecoms\Sms\Block\Customer;


class ForgotPassword extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;

    /**
     * @var int 
     */
    private $_username = -1;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->_customerSession = $customerSession;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout(){
        parent::_prepareLayout();
        if(!$this->helper->isEnableMobileLogin() || !$this->helper->getCurrentGateway()){
            $this->setTemplate('Magento_Customer::form/forgotpassword.phtml');
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        if (-1 === $this->_username) {
            $this->_username = $this->_customerSession->getUsername(true);
        }
        return $this->_username;
    }

    /**
     * Check if autocomplete is disabled on storefront
     *
     * @return bool
     */
    public function isAutocompleteDisabled()
    {
        return (bool)!$this->_scopeConfig->getValue(
            \Magento\Customer\Model\Form::XML_PATH_ENABLE_AUTOCOMPLETE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getOtpLength(){
        return $this->helper->getOtpLength();
    }

    /**
     * @return bool
     */
    public function isEnabledVerifying(){
        return $this->helper->isEnableVerifyingCustomerMobile() &&
            $this->helper->isEnableVerifyingOnRegister();
    }

    /**
     * @return mixed
     */
    public function getCustomer(){
        return $this->_customerSession->getCustomer();
    }

    /**
     * Send OTP URL
     *
     * @return string
     */
    public function getSendOtpUrl(){
        return $this->getUrl('vsms/otp/sendforgotpassword');
    }

    /**
     * Send OTP URL
     *
     * @return string
     */
    public function getVerifyOtpUrl(){
        return $this->getUrl('vsms/otp/verifyforgotpassword');
    }

    /**
     * Get otp resend period time
     *
     * @return number
     */
    public function getOtpResendPeriodTime(){
        return $this->helper->getOtpResendPeriodTime();
    }
}
