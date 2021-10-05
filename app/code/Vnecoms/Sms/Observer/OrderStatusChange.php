<?php
/**
 * Copyright © Vnecoms. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vnecoms\Sms\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;

class OrderStatusChange implements ObserverInterface
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Email\Model\Template\Filter
     */
    protected $filter;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;


    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param \Magento\Email\Model\Template\Filter $filter
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Email\Model\Template\Filter $filter,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->helper = $helper;
        $this->filter = $filter;
        $this->customerFactory = $customerFactory;
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
        
        $order = $observer->getOrder();
        $originStatus = $order->getOrigData('status');
        $status = $order->getStatus();
        if (!$originStatus || !$status || $originStatus == $status) {
            return;
        }

        /* Send notification message to customer when order status is changed*/
        if (!$this->helper->canSendOrderStatusChangedMessage($order->getStoreId())) return;
        
        $customer = $this->helper->getCustomerObjectForSendingSms($order);
        
        $messages = $this->helper->getOrderStatusChangedMessage($order->getStoreId());
        
        if (empty($messages)) return;
        
        $messageConvertedToArray = json_decode($messages, true);
        if (!is_array($messageConvertedToArray)) return;
        
        foreach ($messageConvertedToArray as $key => $message) {
            if (
                isset($message['order_status']) &&
                isset($message['message']) &&
                ($status == $message['order_status'])
            ) {
                $this->filter->setVariables([
                    'order' => $order,
                    'customer' => $customer,
                    'order_status' => $order->getStatusLabel()
                ]);
                $message = $this->filter->filter($message['message']);
                $this->helper->sendCustomerSms($customer, $message);
            }
        }
    }
}
