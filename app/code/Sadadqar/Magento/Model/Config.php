<?php

namespace Sadadqar\Magento\Model;

use \Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const KEY_ACTIVE = 'active';
    const KEY_PRIVATE_KEY = 'key_secret';
	const KEY_PUBLIC_KEY= 'key_id';
	const KEY_WEBSITE= 'sadad_website';
	const KEY_CHEKOUTTYPE= 'checkout_type';
	const KEY_CHECKOUTTYPE2= 'checkout2type';
	const KEY_CHECKLANG= 'sadad_lang';
    const KEY_PAYMENT_ACTION = 'payment_action';
	const KEY_HIDELOAD = 'hide_loader';

    /**
     * @var string
     */
    protected $methodCode = 'sadadqa';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var int
     */
    protected $storeId = null;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getMerchantNameOverride()
    {
        return $this->getConfigData(self::KEY_PRIVATE_KEY);
    }

    public function getKeyId()
    {
        return $this->getConfigData(self::KEY_PUBLIC_KEY);
    }
    
    public function getPaymentAction()
    {
        return $this->getConfigData(self::KEY_PAYMENT_ACTION);
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param null|string $storeId
     *
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->storeId;
        }

        $code = $this->methodCode;

        $path = 'payment/' . $code . '/' . $field;
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool) (int) $this->getConfigData(self::KEY_ACTIVE, $this->storeId);
    }
	/**
     * To check billing country is allowed for the payment method
     *
     * @param string $country
     * @return bool
     */
    public function canUseForCountry($country)
    {
        /*
        for specific country, the flag will set up as 1
        
        if ($this->getConfigData(self::KEY_ALLOW_SPECIFIC) == 1) {
            $availableCountries = explode(',', $this->getConfigData(self::KEY_SPECIFIC_COUNTRY));
            if (!in_array($country, $availableCountries)) {
                return false;
            }
        }*/

        return true;
    }
}
