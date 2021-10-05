<?php
namespace Vnecoms\Sms\Controller\Otp\Login;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Vnecoms\Sms\Model\Mobile;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Send extends \Vnecoms\Sms\Controller\Otp\Checkout\Send implements HttpPostActionInterface
{
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        try{
            $mobileNum = $this->getRequest()->getParam('mobile');
            $customerId = $this->customerSession->getCustomerId();
            $otpGenerator = $this->_objectManager->create('Vnecoms\Sms\Model\Otp\Generator');
            $helper = $this->_objectManager->create('Vnecoms\Sms\Helper\Data');

            /* Save the mobile number*/
            $mobile = $this->_objectManager->create('Vnecoms\Sms\Model\Mobile');

            if($customerId){
                $mobile->load($customerId, 'customer_id');
            }else{
                $mobileCollection = $mobile->getCollection()
                    ->addFieldToFilter('mobile', $mobileNum)
                    ->addFieldToFilter('status', Mobile::STATUS_NOT_VERIFIED)
                    ->addFieldToFilter('customer_id', ['null' => true]);
                if($mobileCollection->count()){
                    $mobile = $mobileCollection->getFirstItem();
                }
            }

            $isResend = $this->getRequest()->getParam('resend');
            $currentResendCount = (int) $this->customerSession->getOtpResendCount();
            /* Block customer if he is sending OTP too much times.*/


            if($isResend){
                $currentResendCount++;
                $this->customerSession->setLastTimeResendOtp($this->date->timestamp());
                $this->customerSession->setOtpResendCount($currentResendCount);
            }

            if(
                $mobile->getMobileId() &&
                !$mobile->isExpiredOTP()
            ) {
                $otp = $mobile->getOtp();
            }else{
                $otp = $otpGenerator->generateCode();
            }
            $mobile->addData([
                'customer_id' => $customerId?$customerId:null,
                'mobile' => $mobileNum,
                'additional_data' => $this->getRequest()->getParam('secure_key'),
                'otp' => $otp,
                'otp_created_at' => $this->date->timestamp(),
                'status' => 0,
            ])->save();

            /* Send otp Message*/
            $message = $helper->getOtpMessage();
            $this->filter->setVariables(['otp_code' => $otp]);
            $message = $this->filter->filter($message);
            $helper->sendSms($mobileNum, $message);

            $data = [
                'success' => true,
                'resend' => $this->customerSession->getData('otp_resend_count')
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
