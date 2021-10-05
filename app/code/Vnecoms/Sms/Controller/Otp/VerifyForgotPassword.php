<?php
namespace Vnecoms\Sms\Controller\Otp;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Vnecoms\Sms\Model\Mobile;
use Magento\Framework\App\Action\HttpPostActionInterface;

class VerifyForgotPassword extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
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
     * @var \Magento\Customer\Model\AccountManagement
     */
    protected $accountmanagement;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * VerifyForgotPassword constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CustomerSession $customerSession
     * @param DateTime $date
     * @param \Magento\Email\Model\Template\Filter $filter
     * @param \Magento\Customer\Model\AccountManagement $accountmanagement
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CustomerSession $customerSession,
        DateTime $date,
        \Magento\Email\Model\Template\Filter $filter,
        \Magento\Customer\Model\AccountManagement $accountmanagement,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->date = $date;
        $this->filter = $filter;
        $this->urlBuilder = $context->getUrl();
        $this->accountmanagement = $accountmanagement;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
    }

    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        try{
            /*get otp collection filter by mobile and otp*/
            $mobileNum = $this->getRequest()->getParam('mobile');
            $otp = trim($this->getRequest()->getParam('otp'));

            /* Save the mobile number*/
            /*get the otp object from collection above and get customer from that object*/
            $collection = $this->_objectManager->create('Vnecoms\Sms\Model\ResourceModel\Mobile\Collection')
                ->addFieldToFilter('mobile', $mobileNum)
                ->addFieldToFilter('otp', $otp);

            /*If the collection count is zero just throw error message*/
            if(!$collection->count()){
                throw new \Exception(__("The OTP code is not valid."));
			}

			$otpObj = $collection->getFirstItem();
            $customerId = $otpObj->getData('customer_id');
			/*Get customer object from customer id*/
            $customer = $this->_customerRepositoryInterface->getById($customerId);
			
            $mobile = $collection->getFirstItem();
            $helper = $this->_objectManager->create('Vnecoms\Sms\Helper\Data');
            if((strtotime($mobile->getOtpCreatedAt()) + $helper->getOtpExpiredPeriodTime()) < $this->date->timestamp()){
                throw new \Exception(__("The OTP code is expired."));
            }
            $mobile->delete();
            $this->customerSession->setOtpResendCount(0);

            /*generate the reset password link and save the reset password token*/
            $helper = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\User\Helper\Data');
            $newResetPasswordLinkToken = $helper->generateResetPasswordLinkToken();
            $this->accountmanagement->changeResetPasswordLinkToken($customer, $newResetPasswordLinkToken);

            /*Return the reset link*/
            $data = [
                'success' => true,
                'otp' => $otp,
                'customerId' => $customerId,
                'url' => $this->urlBuilder->getUrl('customer/account/createPassword', ['_query' => ['id' =>$customerId, 'token' => $newResetPasswordLinkToken,]])
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
