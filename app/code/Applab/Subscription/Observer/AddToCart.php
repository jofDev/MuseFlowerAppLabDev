<?php
namespace Applab\Subscription\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Http\Context as customerSession;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Applab\Subscription\Helper\Data;
 
class AddToCart implements ObserverInterface{
    protected $cart;
    protected $messageManager;
    protected $redirect;
    protected $request;
    protected $product;
    protected $customerSession;
    protected $attributeSetRepository;
 
    public function __construct(
        RedirectInterface $redirect,
        Cart $cart,
        ManagerInterface $messageManager,
        RequestInterface $request,
        Product $product,
        customerSession $session,
        AttributeSetRepositoryInterface $attributeSetRepository,
        CheckoutSession $checkoutSession,
        Data $helper
    )
    {
        $this->redirect = $redirect;
        $this->cart = $cart;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->product = $product;
        $this->customerSession = $session;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
    }
 
    /**
     * add to cart event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {  
        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
         $cartObject = $objectManager->create('Magento\Checkout\Model\Cart')->truncate(); 
         $cartObject->saveQuote();*/

        $postValues     = $this->request->getPostValue();        
        $cart           = $this->cart;
        $quoteItems     = $this->checkoutSession->getQuote()->getItemsCollection();  
     
        if(count($quoteItems) > 0 && $this->helper->cartHasSubscriptionProduct($quoteItems) != 0 ) {
            $observer->getRequest()->setParam('product', false);
            $this->messageManager->addNotice(__('Subscription product exists in cart. Please remove it and try again .'));
            return $this;
        }
        $productId      = $postValues['product'];

        if ($this->helper->isSubscriptionProductById($productId) == 1) {

            foreach($quoteItems as $item)
            {
                if($item->getProductId() != $productId){
                        $cart->removeItem($item->getId())->save();
                } 
            }
            $this->messageManager->addNotice(__('Cleared all items from cart and added subscription product to cart !'));
            return $this;
        }
 
        return $this;
    }
}