<?php
    namespace Applab\ProductComment\Observer;
 
    use Magento\Framework\Event\ObserverInterface;
    use Magento\Framework\App\RequestInterface;
 
    class SetCustomerComment implements ObserverInterface
    {
        public function __construct(
            \Magento\Framework\App\RequestInterface $request
        )
        {
            $this->_request = $request;
        }
        public function execute(\Magento\Framework\Event\Observer $observer) {

            $data = $this->_request->getPost(); 
            if($data->user_comment){
                $quoteItem  = $observer->getQuoteItem();
                $quoteItem->setCustomerComment($data->user_comment);
            }
        }
 
    }
