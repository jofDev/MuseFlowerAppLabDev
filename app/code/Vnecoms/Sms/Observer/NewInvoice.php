<?php
namespace Vnecoms\Sms\Observer;

use Magento\Framework\Event\ObserverInterface;

class NewInvoice implements ObserverInterface
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
     * @var \Vnecoms\Sms\Model\ResourceModel\Sms\CollectionFactory
     */
    protected $smsCollectionFactory;
    
    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param \Magento\Email\Model\Template\Filter $filter
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Vnecoms\Sms\Model\ResourceModel\Sms\CollectionFactory $smsCollectionFactory
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Email\Model\Template\Filter $filter,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Vnecoms\Sms\Model\ResourceModel\Sms\CollectionFactory $smsCollectionFactory
    ){
        $this->helper = $helper;
        $this->filter = $filter;
        $this->customerFactory = $customerFactory;
        $this->smsCollectionFactory = $smsCollectionFactory;
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
        
        $invoice    = $observer->getInvoice();
        $order      = $invoice->getOrder();
        
        $additionalData = 'invoice|'.$invoice->getId();
        
        /* Check if the SMS is sent already*/
        $collection = $this->smsCollectionFactory->create()
            ->addFieldToFilter('additional_data',['like' => $additionalData]);
        if($collection->count()) return;

        /* Send notification message to customer when a new invoice is created*/
        if(
            $this->helper->canSendNewInvoiceMessage($order->getStoreId())
        ) {
            $customer = $this->helper->getCustomerObjectForSendingSms($order);
            
            $message = $this->helper->getNewInvoiceMessage($order->getStoreId());
            $this->filter->setVariables([
                'order' => $order,
                'invoice' => $invoice,
                'customer' => $customer,
                'invoice_total' => $order->formatPriceTxt($invoice->getGrandTotal()),
                'invoice_total_amount' => $order->getOrderCurrency()->formatTxt(
                    $invoice->getGrandTotal(),
                    ['display' => \Magento\Framework\Currency::NO_SYMBOL]
                ),
            ]);
            $message = $this->filter->filter($message);
            $this->helper->sendCustomerSms($customer, $message, $additionalData);
        }
        
    }
}
