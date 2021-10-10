<?php

namespace Applab\Subscription\Observer;
use Magento\Sales\Model\Order;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session;
use Applab\Subscription\Helper\Data;
class MultiShip implements ObserverInterface
{

    /**
     * Constructor
     *
     * @param Session $checkoutSession
     */
    public function __construct(
        Session $checkoutSession,
        \Magento\Backend\App\Action\Context $context,        
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        Data $helper,
        \Applab\Subscription\Model\SubscriptionDataFactory $subscriptionDataFactory
    ) {
        $this->checkoutSession      = $checkoutSession;
        $this->productRepository    = $productRepository;
        $this->orderRepository      = $orderRepository;
        $this->cartItemFactory      = $cartItemFactory;
        $this->quoteRepository      = $quoteRepository;
        $this->orderItemFactory     = $orderItemFactory;
        $this->helper               = $helper;
        $this->subscriptionDataFactory  = $subscriptionDataFactory;
    }

    /**
     * 
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \Magento\GoogleAdwords\Observer\SetConversionValueObserver
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderIn = $observer->getEvent()->getData('order');
        $orderId = $orderIn->getId(); 

        $order   = $this->orderRepository->get($orderId);
        $quote   = $this->quoteRepository->get($order->getQuoteId());

        if(count($order->getAllItems()) == 1){

            foreach ($order->getAllItems() as $orderItem) {
                $productId = $orderItem->getProductId();
            }

            if ($this->helper->isSubscriptionProductById($productId) == 1) {

                $product            = $this->productRepository->getById($productId);
                $subscription_type  = $subscription_count = '';
                $attributeCode      = $this->helper::SUBCRIPTION_ATTRIBUTE_TYPE_CODE;
                $isAttributeExist   = $product->getResource()->getAttribute($attributeCode); 
                if ($isAttributeExist && $isAttributeExist->usesSource()) {
                    $subscription_type  = $product->getResource()->getAttribute($attributeCode)->setStoreId(0)->getFrontend()->getValue($product);
                }
                if($subscription_type){
                    
                    $subscription_count = ($subscription_type == $this->helper::SUBCRIPTION_ATTRIBUTE_TYPE_MONTH) ? 1 : 3;
                    $days_to_add        = ($subscription_type == $this->helper::SUBCRIPTION_ATTRIBUTE_TYPE_MONTH) ? 30 : 7;
                    $shipment_count     = $subscription_count + 1 ;
                    $shipment_dates[]   = date("M j Y", strtotime($order->getCreatedAt())); // change it to customer defined date
                    $start_date         = $order->getCreatedAt(); // change it to customer defined date

                    for ($i=0; $i < $subscription_count; $i++) {     
                        $quoteItem  = $this->cartItemFactory->create();
                        $quoteItem->setProduct($product);
                        $quoteItem->getProduct()->setIsSuperMode(true);
                        $quote->addItem($quoteItem);
                        $quote->collectTotals()->save();

                        $orderItem  = $this->orderItemFactory->create();
                        $orderItem
                        ->setStoreId($order->getStoreId())
                        ->setQuoteItemId($quoteItem->getId())
                        ->setProductId($product->getId())
                        ->setProductType($product->getTypeId())
                        ->setName($product->getName())
                        ->setSku($product->getSku())
                        ->setQtyOrdered(1)
                        ->setPrice(0)
                        ->setWeight(1)
                        ->setIsVirtual(0);
                        $order->addItem($orderItem);

                        $next_date        = date("Y-m-d H:i:s", strtotime($start_date. "+".$days_to_add." days")); 
                        $shipment_dates[] = date("M j Y", strtotime($next_date));
                        $start_date       = $next_date;
                    }
                    $this->orderRepository->save($order);

                    // add in subscription table 

                    $subscriptionData = $this->subscriptionDataFactory->create();
                    $subscriptionData->setStoreId($order->getStore()->getId());
                    $subscriptionData->setOrderId($order->getId());
                    $subscriptionData->setOrderIncId($order->getIncrementId());
                    $subscriptionData->setCustomerId($order->getCustomerId());
                    $subscriptionData->setCustomerName($order->getCustomerName());
                    $subscriptionData->setCustomerEmail($order->getCustomerEmail());
                    $subscriptionData->setProductId($product->getId());
                    $subscriptionData->setProductSku($product->getSku());
                    $subscriptionData->setSubscriptionType($subscription_type);
                    $subscriptionData->setSubscriptionStart(date('Y-m-d H:i:s'));
                    $subscriptionData->setSubscriptionEnd(date('Y-m-d H:i:s'));
                    $subscriptionData->setShipmentDates(implode(", ",$shipment_dates));
                    $subscriptionData->setShipmentCount($shipment_count);
                    $subscriptionData->setStatus('In progress');
                    $subscriptionData->setCreatedAt(date('Y-m-d H:i:s'));
                    $subscriptionData->save();
                }
                
            }
        }
                  
    }


}