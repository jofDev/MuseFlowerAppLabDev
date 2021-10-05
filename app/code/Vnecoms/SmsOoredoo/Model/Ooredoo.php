<?php
namespace Vnecoms\SmsOoredoo\Model;

use Vnecoms\Sms\Model\Sms;
use GuzzleHttp\json_decode;

class Ooredoo implements \Vnecoms\Sms\Model\GatewayInterface
{
    /**
     * @var \Vnecoms\SmsOoredoo\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
    /**
     * @param \Vnecoms\SmsOoredoo\Helper\Data $helper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Vnecoms\SmsOoredoo\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    ){
        $this->helper = $helper;
        $this->logger = $logger;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\Sms\Model\GatewayInterface::getTitle()
     */
    public function getTitle(){
        return __("www.ooredoo.qa");
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\Sms\Model\GatewayInterface::validateConfig()
     */
    public function validateConfig(){
        return $this->helper->getUser() && $this->helper->getPassword()&& $this->helper->getSender() && $this->helper->getCustomerId();
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\Sms\Model\GatewayInterface::sendSms()
     */
    public function sendSms($number, $message){
        $user           = $this->helper->getUser();
        $pass           = $this->helper->getPassword();
        $sender         = $this->helper->getSender();
        $customerId     = $this->helper->getCustomerId();
        $messageType    = $this->helper->getMessageType();
        
        $number = str_replace('+', '', $number);
        
        $client = new \Vnecoms\SmsOoredoo\Http\Client($user, $pass, $customerId);
        $response = $client->sendSms($number, $message, $sender, $messageType);
        $responseData = simplexml_load_string($response);
        if(!$responseData) return ['status' => Sms::STATUS_FAILED, 'note' => $response];
        
        $result = [
            'sid'       => $responseData->TransactionID,
            'status'    => $this->getMessageStatus($responseData->Result),
            'note'      => json_encode((array)$responseData),
        ];

        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\Sms\Model\GatewayInterface::getMessageStatus()
     */
    public function getMessageStatus($status){
        $status = strtoupper($status);
        $result = Sms::STATUS_FAILED;
        switch($status){
            case "OK":
                $result = Sms::STATUS_SENT;
                break;
        }
    
        return $result;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Vnecoms\Sms\Model\GatewayInterface::getSms()
     */
    public function getSms($sid){
        
        return null;
    }
}
