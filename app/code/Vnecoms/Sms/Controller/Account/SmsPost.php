<?php
namespace Vnecoms\Sms\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class SmsPost extends \Magento\Customer\Controller\AbstractAccount implements HttpPostActionInterface
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     * @var \Vnecoms\Sms\Model\MobileFactory
     */
    protected $mobileFactory;
    
    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Vnecoms\Sms\Model\MobileFactory $mobileFactory
     */
    public function __construct(
        Context $context,
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Vnecoms\Sms\Model\MobileFactory $mobileFactory
    ) {
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->mobileFactory = $mobileFactory;
        parent::__construct($context);
    }
    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $mobileNum = $this->getRequest()->getParam('mobile_number');
        $otp = $this->getRequest()->getParam('mobile_otp');
        try{
            if(!$mobileNum){
                throw new \Exception(__("Please enter your mobile number."));
            }
            
            $customer = $this->customerSession->getCustomer();
            if(
                $customer->getMobilenumber() != $mobileNum &&
                $this->helper->isEnableVerifyingCustomerMobile()
            ) {
                $mobile = $this->mobileFactory->create();
                $collection = $mobile->getCollection()
                    ->addFieldToFilter('mobile', $mobileNum)
                    ->addFieldToFilter('otp', $otp);
                if(!$collection->count()) throw new \Exception(__("The otp is not valid."));
                
            }
            
            $customer->setData('mobilenumber', $mobileNum)->save();
            
            /* Delete all otp rows relate to mobule num*/
            $collection = $this->mobileFactory->create()->getCollection()
                ->addFieldToFilter('mobile', $mobileNum);
            foreach($collection as $mobile){
                $mobile->delete();
            }
            
            $this->messageManager->addSuccess(__("The mobile number is updated."));
        }catch(\Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
        
        $this->_redirect('customer/account/sms');
    }
}
