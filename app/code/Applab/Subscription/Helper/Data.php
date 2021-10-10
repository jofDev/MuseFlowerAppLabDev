<?php
namespace Applab\Subscription\Helper;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeSetRepositoryInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	const SUBCRIPTION_ATTRIBUTE_SET_NAME 	= 'Subscription Product';
	const SUBCRIPTION_ATTRIBUTE_TYPE_CODE 	= 'subscription_type';
	const SUBCRIPTION_ATTRIBUTE_TYPE_MONTH 	= 'monthly';
	const SUBCRIPTION_ATTRIBUTE_TYPE_WEEK 	= 'weekly';

	public function __construct(
        Product $product,
        AttributeSetRepositoryInterface $attributeSetRepository
    )
    {

        $this->product = $product;
        $this->attributeSetRepository = $attributeSetRepository;
    }

    public function cartHasSubscriptionProduct($quoteItems){
    	$result = 0;
    	foreach($quoteItems as $item)
        {
           	$product        = $this->product->load($item->getProductId());
        	$attributeSet   = $this->attributeSetRepository->get($product->getAttributeSetId());

        	if ($attributeSet->getAttributeSetName() == self::SUBCRIPTION_ATTRIBUTE_SET_NAME) {
        		$result = 1;
        	}
        }
        return $result;
    }

    public function isSubscriptionProductById($productId){
    	
          	$product        = $this->product->load($productId);
        	$attributeSet   = $this->attributeSetRepository->get($product->getAttributeSetId());

        	if ($attributeSet->getAttributeSetName() == self::SUBCRIPTION_ATTRIBUTE_SET_NAME) {
        		return 1;
        	}
        	return 0;
    }
	


}