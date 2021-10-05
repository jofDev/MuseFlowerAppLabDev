<?php
namespace Vnecoms\Sms\Model\Otp;


class Generator extends \Magento\Framework\DataObject
{    
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $_helper;
    
    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper
    ) {
        $this->_helper = $helper;
    }
    
    /**
     * Generate coupon code
     *
     * @return string
     */
    public function generateCode()
    {
        $format = $this->_helper->getOtpFormat();
        if (empty($format)) {
            $format = \Vnecoms\Sms\Model\Config\Source\CodeFormat::TYPE_ALPHANUM;
        }
    
        $charset = $this->_helper->getCharset();
    
        $code = '';
        $charsetSize = count($charset);
        $length = max(1, $this->_helper->getOtpLength());
        for ($i = 0; $i < $length; ++$i) {
            $char = $charset[\Magento\Framework\Math\Random::getRandomNumber(0, $charsetSize - 1)];
            $code .= $char;
        }
    
        return $code;
    }
    
    /**
     * Generate OTP code
     *
     * @return string
     */
    public function generateBidderName()
    {
        $code = $this->generateCode();
        return $code;
    }
}
