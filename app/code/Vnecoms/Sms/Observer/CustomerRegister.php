<?php
namespace Vnecoms\Sms\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerRegister implements ObserverInterface
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Email\Model\Template\Filter
     */
    protected $filter;
    
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
    
    /**
     * @var \Vnecoms\Sms\Model\MobileFactory
     */
    protected $mobileFactory;
    
    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param \Magento\Email\Model\Template\Filter $filter
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Vnecoms\Sms\Model\MobileFactory $mobileFactory
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Email\Model\Template\Filter $filter,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Vnecoms\Sms\Model\MobileFactory $mobileFactory
    ){
        $this->helper = $helper;
        $this->filter = $filter;
        $this->customerFactory = $customerFactory;
        $this->mobileFactory = $mobileFactory;
    }
    
    /**
     * Vendor Save After
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->helper->getCurrentGateway()) return;
        
        $customer = $observer->getCustomer();
        if(!$customer instanceof \Magento\Customer\Model\Customer){
            $customer = $this->customerFactory->create()->load($customer->getId());
        }
        /* Send notification message to admin when a new customer registered*/
        if($this->helper->canSendCustomerRegisterMessageToAdmin()){
            $message = $this->helper->getCustomerRegisterMessageSendToAdmin();
            $this->filter->setVariables(['customer' => $customer]);
            $message = $this->filter->filter($message);
            $this->helper->sendAdminSms($message);
        }
        
        /* Save customer mobile*/
        $controller = $observer->getAccountController();
        $mobileNum = $controller->getRequest()->getParam('mobile_number');
        $otp = $controller->getRequest()->getParam('mobile_otp');
        if(!$mobileNum) return;
        
        $mobile = $this->mobileFactory->create();
        
        if(
            $this->helper->isEnableVerifyingCustomerMobile() && 
            $this->helper->isEnableVerifyingOnRegister()
        ){
            $collection = $mobile->getCollection()
                ->addFieldToFilter('mobile', $mobileNum)
                ->addFieldToFilter('otp', $otp);
            if(!$collection->count()) return;
        }
        
        $customer->setData('mobilenumber', $mobileNum)->save();
        
        /* Delete all otp rows relate to mobule num*/
        $collection = $this->mobileFactory->create()->getCollection()
            ->addFieldToFilter('mobile', $mobileNum);
        foreach($collection as $mobile){
            $mobile->delete();
        }
        
        
        if(
            $this->helper->isEnableVerifyingCustomerMobile() &&
            !$this->helper->isEnableVerifyingOnRegister()
        ) {
            return;
        }
        
        /* Send vendor account approved sms message*/
        if(
            (
                !$this->helper->isEnableVerifyingCustomerMobile() ||
                (
                    $this->helper->isEnableVerifyingCustomerMobile() &&
                    $this->helper->isEnableVerifyingOnRegister()
                )
            ) &&
            $this->helper->canSendCustomerRegisterMessage()
        ){
            $message = $this->helper->getCustomerRegisterMessage();
            $this->filter->setVariables(['customer' => $customer]);
            $message = $this->filter->filter($message);
            $this->helper->sendCustomerSms($customer, $message);
        }
        return $this;
    }
}
