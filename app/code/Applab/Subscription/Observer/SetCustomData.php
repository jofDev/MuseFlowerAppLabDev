<?php
    namespace Applab\Subscription\Observer;
 
    use Magento\Framework\Event\ObserverInterface;
    use Magento\Framework\App\RequestInterface;
 
    class SetCustomData implements ObserverInterface
    {
        public function __construct(
            \Magento\Framework\App\RequestInterface $request
        )
        {
            $this->_request = $request;
        }
        public function execute(\Magento\Framework\Event\Observer $observer) {

            $data = $this->_request->getPost(); 
            
            if($data->delivery_date){
                $quoteItem  = $observer->getQuoteItem();
                $quoteItem->setDeliveryDate($data->delivery_date);
            }
            if($data->delivery_time){
                $quoteItem  = $observer->getQuoteItem();
                $quoteItem->setDeliveryTime($data->delivery_time);
            }
            if($data->gift_qrcode){
                $quoteItem  = $observer->getQuoteItem();
                $quoteItem->setGiftQrcode($data->gift_qrcode);
            }

        }
 
    }