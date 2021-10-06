<?php

namespace Sadadqar\Magento\Controller;

use Sadadqar\Magento\Model\Config;
use Magento\Framework\App\RequestInterface;

abstract class BaseControllerBk extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Sadadqar\Magento\Model\CheckoutFactory
     */
    protected $checkoutFactory;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote = false;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Sadadqar\Magento\Model\Checkout
     */
    protected $checkout;
	
	 protected $urlInterface;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Sadadqar\Magento\Model\Config $config
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Sadadqar\Magento\Model\Config $config
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->config = $config;
		

        $this->key_id = $this->config->getConfigData(Config::KEY_PUBLIC_KEY);
        $this->key_secret = $this->config->getConfigData(Config::KEY_PRIVATE_KEY);
		$this->website = $this->config->getConfigData(Config::KEY_WEBSITE);
		$this->checkLang = $this->config->getConfigData(Config::KEY_CHECKLANG);
		$this->checkoutType = $this->config->getConfigData(Config::KEY_CHEKOUTTYPE);
		$this->checkoutType2 = $this->config->getConfigData(Config::KEY_CHECKOUTTYPE2);
		$this->hideLoader = $this->config->getConfigData(Config::KEY_HIDELOAD);
		
		

    }

    /**
     * Instantiate quote and checkout
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function initCheckout()
    {
        $quote = $this->getQuote();
		
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->getResponse()->setStatusHeader(403, '1.1', 'Forbidden');
            throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t initialize checkout.'));
        }
    }

    /**
     * Return checkout quote object
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        if (!$this->quote) {
            $this->quote = $this->checkoutSession->getQuote();
        }
        return $this->quote;
    }

    /**
     * @return \Sadadqar\Magento\Model\Checkout
     */
    protected function getCheckout()
    {
        if (!$this->checkout) {
            $this->checkout = $this->checkoutFactory->create(
                [
                    'params' => [
                        'quote' => $this->checkoutSession->getQuote(),
                    ],
                ]
            );
        }
        return $this->checkout;
    }
	
}