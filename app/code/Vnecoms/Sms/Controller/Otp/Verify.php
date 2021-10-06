<?php
namespace Vnecoms\Sms\Controller\Otp;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Vnecoms\Sms\Model\Mobile;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Verify extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;
    
    /**
     * @var \Magento\Email\Model\Template\Filter
     */
    protected $filter;
    
    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CustomerSession $customerSession
     * @param DateTime $date
     * @param \Magento\Email\Model\Template\Filter $filter
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CustomerSession $customerSession,
        DateTime $date,
        \Magento\Email\Model\Template\Filter $filter
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->date = $date;
        $this->filter = $filter;
        parent::__construct($context);
    }
    
    
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        try{
            $mobileNum = $this->getRequest()->getParam('mobile');
            $otp = trim($this->getRequest()->getParam('otp'));
            $helper = $this->_objectManager->create('Vnecoms\Sms\Helper\Data');
            if($helper->isUniqueMobileNumber()){
                /* Check if the mobile is used by other */
                $collection = $this->_objectManager->create('Magento\Customer\Model\Customer')->getCollection()
                    ->addAttributeToFilter('mobilenumber', $mobileNum)
                    ->addAttributeToFilter('entity_id', ['neq' => $this->customerSession->getId()]);
                
                if($collection->count()){
                    throw new \Magento\Framework\Exception\LocalizedException(__("The mobile number is used by another customer account."));
                }
            }
            
            /* Save the mobile number*/
            $collection = $this->_objectManager->create('Vnecoms\Sms\Model\ResourceModel\Mobile\Collection')
                ->addFieldToFilter('mobile', $mobileNum)
                ->addFieldToFilter('otp', $otp);
            
            if(!$collection->count()){
                throw new \Exception(__("The OTP code is not valid."));
            }
            
            $mobile = $collection->getFirstItem();
            
            $helper = $this->_objectManager->create('Vnecoms\Sms\Helper\Data');

            if((strtotime($mobile->getOtpCreatedAt()) + $helper->getOtpExpiredPeriodTime()) < $this->date->timestamp()){
                throw new \Exception(__("The OTP code is expired."));
            }
            
            $mobile->setStatus(Mobile::STATUS_VERIFIED)->save();
            $this->customerSession->setOtpResendCount(0);
            $data = [
                'success' => true,
                'otp' => $otp,
            ];
        }catch(\Exception $e){
            $data = [
                'success' => false,
                'msg' => $e->getMessage(),
            ];
        }
        
        
        $response->setData($data);
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}
