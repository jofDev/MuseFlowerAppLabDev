<?php

namespace Vnecoms\Sms\Block\Checkout;

/**
 * Class LayoutProcessor
 */
class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession;
    }
    
    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if(!$this->helper->getCurrentGateway()) return $jsLayout;

        /*Shipping mobile*/
        $telephoneData = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['config'];
        $telephoneData['elementTmpl'] = 'Vnecoms_Sms/checkout/mobile';
        $telephoneData['initialCountry'] = strtolower($this->helper->getInitialCountry());
        $telephoneData['geoIpUrl'] = $this->helper->getGeoIpDatabase()?$this->urlBuilder->getUrl('vsms/geoip'):'https://ipinfo.io';
        $telephoneData['allowDropdown'] = $this->helper->getAllowCountryDropdown();
        $allowedCountries = $this->helper->isAllowedAllCountries();
        $telephoneData['onlyCountries'] = $allowedCountries?false:explode(',',$this->helper->getAllowedCountries());
        $preferredCountries = $this->helper->getPreferredCountries();
        $preferredCountries = $preferredCountries?explode(',', $preferredCountries):["us", "vn"];
        $telephoneData['preferredCountries'] = $preferredCountries;
        $telephoneData['requireVerifying'] = $this->helper->isEnableVerifyingAddressMobile();
        $telephoneData['sendOtpUrl'] = $this->urlBuilder->getUrl('vsms/otp_checkout/send');
        $telephoneData['verifyOtpUrl'] = $this->urlBuilder->getUrl('vsms/otp_checkout/verify');
        $telephoneData['otpResendPeriodTime'] = $this->helper->getOtpResendPeriodTime();
        $telephoneData['defaultResendBtnLabel'] = __('Resend');
        if($this->customerSession->isLoggedIn()){
            $telephoneData['customerMobileNumber'] = $this->customerSession->getCustomer()->getMobilenumber();
        }
        $telephoneData['component'] = 'Vnecoms_Sms/js/checkout/mobile';
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['config'] = $telephoneData;

        /* Checkout Authentication*/
		if(isset($jsLayout['components']['checkout']['children']['authentication'])){
			$authenticationData = $jsLayout['components']['checkout']['children']['authentication'];
			$authenticationData['component'] = 'Vnecoms_Sms/js/checkout/authentication';
			$authenticationData['template'] = 'Vnecoms_Sms/checkout/authentication';
			$authenticationData['initialCountry'] = $telephoneData['initialCountry'];
			$authenticationData['geoIpUrl'] = $telephoneData['geoIpUrl'];
			$authenticationData['allowDropdown'] = $telephoneData['allowDropdown'];
			$authenticationData['onlyCountries'] = $telephoneData['onlyCountries'];
			$authenticationData['preferredCountries'] = $telephoneData['preferredCountries'];

			$jsLayout['components']['checkout']['children']['authentication'] = $authenticationData;
		}
		if(
		    $this->helper->isEnableVerifyingAddressMobile() &&
		    isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['additional-payment-validators'])
        ){
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['additional-payment-validators']['children']['vnecoms-sms'] = [
                'component' => 'Vnecoms_Sms/js/view/payment/otp-validator',
            ];
            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['afterMethods']['children']['otp-validation-frm'] = [
                'component' => 'Vnecoms_Sms/js/view/payment/otp-form',
                'customerMobileNumber' => $this->customerSession->getCustomer()->getMobilenumber(),
                'mobileSource' => $this->helper->getMobileSources(),
                'otpResendPeriodTime' => $this->helper->getOtpResendPeriodTime(),
                'defaultResendBtnLabel' => __('Resend'),
                'sendOtpUrl' => $this->urlBuilder->getUrl('vsms/otp_checkout/send'),
                'verifyOtpUrl' => $this->urlBuilder->getUrl('vsms/otp_checkout/verify'),
                'otpLength' => $this->helper->getOtpLength(),
                'links' => ['mobilenumber' => 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.telephone:value'],
            ];
        }
        return $jsLayout;
    }
}
