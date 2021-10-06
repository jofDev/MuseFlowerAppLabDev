<?php
namespace Applab\ProductSku\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class Modifysku extends AbstractModifier
{
	protected $_productCollectionFactory;
        
    public function __construct(     
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Applab\ProductSku\Helper\Data $helper
    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory;    
        $this->helper = $helper;    
    }

	public function modifyMeta(array $meta)
    {	
    	$prefix 	= $this->helper->getSkuPrefix() ? $this->helper->getSkuPrefix() : 'POD-';
	    $defaultSku = $this->_buildSku($prefix);
	    $meta['product-details']['children']['container_sku']['children']['sku']['arguments']['data']['config']['value'] = $defaultSku;
	    $meta['product-details']['children']['container_sku']['children']['sku']['arguments']['data']['config']['disabled'] = 1; 
	   
    	return $meta;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    private function _buildSku($_prefix){

		$collection 		= $this->_productCollectionFactory->create();
		$collection->setOrder('entity_id','DESC')->setPageSize(1)->load();

		$skuToassign = $_prefix.'1';
		
		if($collection->getSize() > 0){
			$product  			= 	$collection->getFirstItem();
			$nextProductId 		= ($product->getId() + 1);
			$skuToassign 		= 	$_prefix.$nextProductId;
		}
		return $skuToassign;
    }
}