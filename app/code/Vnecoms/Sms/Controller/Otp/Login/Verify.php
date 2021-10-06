<?php
namespace Vnecoms\Sms\Controller\Otp\Login;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Vnecoms\Sms\Model\Mobile;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Verify extends \Vnecoms\Sms\Controller\Otp\Checkout\Verify implements HttpPostActionInterface
{
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        try{
            $mobileNum = $this->getRequest()->getParam('mobile');
            $otp = trim($this->getRequest()->getParam('otp'));
            $helper = $this->_objectManager->create('Vnecoms\Sms\Helper\Data');

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
            $this->customerSession->setOtpResendCount(0);

            $mobile->delete();

            $secureKey = $this->getRequest()->getParam('secure_key');
            $email = $this->customerSession->getData($secureKey);
            if(!$email) throw new LocalizedException(__("Can't retrieve login information."));
            try {
                /** @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository */
                $customerRepository = $this->_objectManager->create('Magento\Customer\Api\CustomerRepositoryInterface');
                $customer = $customerRepository->get($email);
                $this->customerSession->setCustomerDataAsLoggedIn($customer);
            } catch (NoSuchEntityException $e) {
                throw new InvalidEmailOrPasswordException(__('Invalid login or password.'));
            }
            $data = [
                'success' => true,
                'email' => $email,
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
