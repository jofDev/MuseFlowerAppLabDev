<?php
namespace Vnecoms\SmsOoredoo\Http;

class Client
{
    const API_URL = 'https://messaging.ooredoo.qa/bms/soap/Messenger.asmx/HTTP_SendSms';
    
    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $pass;
    
    /**
     * @var string
     */
    protected $customerId;
    /**
     * @param string $user
     * @param string $password
     * @param string $customerId
     */
    public function __construct($user, $password, $customerId){
        $this->user = $user;
        $this->pass = $password;
        $this->customerId = $customerId;
    }
    
    /**
     * @param string $destination
     * @param string $text
     * @param string $sender
     * @param string $messageType
     * @return mixed
     */
    public function sendSms($destination, $text, $sender='', $messageType='ArabicWithLatinNumbers') {
        $postBody = [
            'customerID' => $this->customerId,
            'userName'  => $this->user,
            'userPassword' => $this->pass,
            'originator' => $sender,
            'smsText' => urlencode($text),
            'recipientPhone' => $destination,
            'messageType' => $messageType,
            'defDate' => '',
            'blink' => 'false',
            'flash' => 'false',
            'Private' => 'false',
        ];
        return $this->_sendMessage($postBody);
    }
    
    /**
     * Send Message
     * 
     * @param array $postBody
     * @return mixed
     */
    protected function _sendMessage($postBody){
        $params = [];
        foreach ($postBody as $key=>$value){
            $params[] = $key."=".$value;
        }
        $params = implode("&", $params);
        $ch = curl_init( );
        curl_setopt ( $ch, CURLOPT_URL, self::API_URL.'?'.$params );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        // Allow cUrl functions 20 seconds to execute
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
        // Wait 10 seconds while trying to connect
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        $result = curl_exec( $ch );
        curl_close( $ch );
        return $result;
    }
}
