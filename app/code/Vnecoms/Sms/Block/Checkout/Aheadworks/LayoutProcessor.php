<?php

namespace Vnecoms\Sms\Block\Checkout\Aheadworks;

use Magento\Framework\Stdlib\ArrayManager;

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
     * @var ArrayManager
     * @since 101.0.0
     */
    protected $arrayManager;
	
    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Customer\Model\Session $customerSession,
		ArrayManager $arrayManager
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession;
		$this->arrayManager = $arrayManager;
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
		$components = $jsLayout['components'];

        $telephonePath = $this->arrayManager->findPath('telephone', $components, null, 'children');
		$telephone = $this->arrayManager->get($telephonePath ,$components);
		$telephoneData = $telephone['config'];
		if(isset($telephone['validation'])) unset($telephone['validation']);

        /*Shipping mobile*/
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

        $telephone['component'] = 'Vnecoms_Sms/js/checkout/mobile';
		$telephone['config'] = $telephoneData;
		
		$components = $this->arrayManager->set(
			$telephonePath,
			$components,
			$telephone
		);
		$jsLayout['components'] = $components;

        return $jsLayout;
    }
}
