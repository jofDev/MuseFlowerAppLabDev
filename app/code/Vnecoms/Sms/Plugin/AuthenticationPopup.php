<?php
namespace Vnecoms\Sms\Plugin;

class AuthenticationPopup
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
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;


    /**
     * AuthenticationPopup constructor.
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }


    /**
     * @param \Magento\Customer\Block\Account\AuthenticationPopup $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundGetJsLayout(
        \Magento\Customer\Block\Account\AuthenticationPopup $subject,
        \Closure $proceed
    ) {

        $jsLayout = $proceed();
        $jsLayout = $this->serializer->unserialize($jsLayout);
        $auth = $jsLayout['components']['authenticationPopup'];

        $auth['initialCountry'] = strtolower($this->helper->getInitialCountry());
        $auth['geoIpUrl'] = $this->helper->getGeoIpDatabase()?$this->urlBuilder->getUrl('vsms/geoip'):'https://ipinfo.io';
        $auth['allowDropdown'] = $this->helper->getAllowCountryDropdown();
        $allowedCountries = $this->helper->isAllowedAllCountries();
        $auth['onlyCountries'] = $allowedCountries?false:explode(',',$this->helper->getAllowedCountries());
        $preferredCountries = $this->helper->getPreferredCountries();
        $preferredCountries = $preferredCountries?explode(',', $preferredCountries):["us", "vn"];
        $auth['preferredCountries'] = $preferredCountries;
        $jsLayout['components']['authenticationPopup'] = $auth;
        return $this->serializer->serialize($jsLayout);
    }
}
