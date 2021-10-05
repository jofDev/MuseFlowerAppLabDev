<?php

namespace Vnecoms\Sms\Block\Adminhtml\Order\View;

class Mobile extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * Mobile constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);
        $this->customerFactory = $customerFactory;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerMobile(){
        return $this->customerFactory->create()
            ->load($this->getOrder()->getCustomerId())
            ->getData('mobilenumber');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        if(!$this->getOrder()->getCustomerId()) return '';
        return parent::_toHtml();
    }
}
