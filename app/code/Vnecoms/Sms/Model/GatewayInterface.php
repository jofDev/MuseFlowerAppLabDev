<?php
namespace Vnecoms\Sms\Model;

interface GatewayInterface
{
    /**
     * Return payment title
     *
     * @return string
     */
    public function getTitle();
    
    /**
     * Validate gateway config
     * 
     * @return boolean
     */
    public function validateConfig();
    
    /**
     * Send sms
     * @param string $number
     * @param string $message
     * 
     * @return boolean
     */
    public function sendSms($number, $message);
    
    /**
     * Get status of message to save to DB from result message.
     * 
     * @param void $message
     */
    public function getMessageStatus($message);
    
    /**
     * Get Sms detail information from sms gateway
     * 
     * @param string $sid
     */
    public function getSms($sid);
}
