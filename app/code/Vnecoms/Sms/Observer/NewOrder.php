<?php
namespace Vnecoms\Sms\Observer;

use Magento\Framework\Event\ObserverInterface;

class NewOrder implements ObserverInterface
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
    ){
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
        
        $orders = $observer->getOrder();
        if(!$orders) $orders = $observer->getOrders();
        
        if(!is_array($orders)) $orders = [$orders];
        $customer = false;
        
        foreach($orders as $order){
            /* Send notification message to admin when a new order is placed*/
            if($this->helper->canSendNewOrderMessageToAdmin($order->getStoreId())){
                $message = $this->helper->getNewOrderMessageSendToAdmin($order->getStoreId());
                $this->filter->setVariables([
                    'order' => $order,
                    'order_total' => $order->formatPriceTxt($order->getGrandTotal()),
                    'order_total_amount' => $order->getOrderCurrency()->formatTxt(
                        $order->getGrandTotal(),
                        ['display' => \Magento\Framework\Currency::NO_SYMBOL]
                    ),
                ]);
                $message = $this->filter->filter($message);
                $this->helper->sendAdminSms($message);
            }
            
            /* Send notification message to customer when a new order is placed*/
            if(
                $this->helper->canSendNewOrderMessage($order->getStoreId())
            ) {
                $customer = $this->helper->getCustomerObjectForSendingSms($order);
                $message = $this->helper->getNewOrderMessage($order->getStoreId());
                $this->sendSms($order, $customer, $message);
            }
            $orderMessagesByPayment = $this->helper->getNewOrderMessagesByPayment($order->getStoreId());
            $orderMessagesByPayment = json_decode($orderMessagesByPayment, true);
            /* Send new order message by payment method*/
            $payment = $order->getPayment();
            $method = $payment->getMethod();
            if(is_array($orderMessagesByPayment)){
				if(!$customer) $customer = $this->helper->getCustomerObjectForSendingSms($order);
                foreach($orderMessagesByPayment as $message){
                    if($message['payment_method'] != $method) continue;
                    $this->sendSms($order, $customer, $message['message']);
                }
            }
        }
    }
    
    /**
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Framework\DataObject $customer
     * @param string $messageTemplate
     */
    public function sendSms(
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\DataObject $customer,
        $messageTemplate = ''
    ) {
        $this->filter->setVariables([
            'order' => $order,
            'customer' => $customer,
            'order_total' => $order->formatPriceTxt($order->getGrandTotal()),
            'order_total_amount' => $order->getOrderCurrency()->formatTxt(
                $order->getGrandTotal(),
                ['display' => \Magento\Framework\Currency::NO_SYMBOL]
            ),
        ]);
        $message = $this->filter->filter($messageTemplate);
        $this->helper->sendCustomerSms($customer, $message);
    }
}
