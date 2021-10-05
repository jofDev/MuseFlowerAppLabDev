<?php
namespace Vnecoms\Sms\Observer;

use Magento\Framework\Event\ObserverInterface;

class OrderStatusHistorySaveBefore implements ObserverInterface
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
    
    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ){
        $this->helper           = $helper;
        $this->coreRegistry     = $coreRegistry;
        $this->customerFactory  = $customerFactory;
    }
    
    /**
     * Vendor Save After
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->helper->getCurrentGateway()) return;
        
        if($this->coreRegistry->registry('is_sms_notified')){
            $statusHistory = $observer->getStatusHistory();
            $statusHistory->setData('is_customer_notified', 1);
            $order = $statusHistory->getOrder();
            
            $customer = $this->helper->getCustomerObjectForSendingSms($order);
            
            $this->helper->sendSms($customer->getMobilenumber(), $statusHistory->getComment());
        }
    }
}
