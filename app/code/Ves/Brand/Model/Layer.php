<?php
namespace Ves\Brand\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Layer extends \Magento\Catalog\Model\Layer
{
    public function __construct(
        \Magento\Catalog\Model\Layer\ContextInterface $context,
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        AttributeCollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product $catalogProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_coreRegistry = $registry;
        parent::__construct(
            $context,
            $layerStateFactory,
            $attributeCollectionFactory,
            $catalogProduct,
            $storeManager,
            $registry,
            $categoryRepository,
            $data
        );
    }

	public function getProductCollection()
	{
        if (isset($this->_productCollections['brands_products'])) {
            $collection = $this->_productCollections['brands_products'];
        } else {

            $brand = $this->_coreRegistry->registry('current_brand');
            $productIds = $brand->getData('productIds');
            $collection = $this->productCollectionFactory->create();
            $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
                       ->addAttributeToSelect('*')
                       ->addFieldToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                       ->addFieldToFilter('entity_id',['in'=>$productIds]);
                       
            $this->prepareProductCollection($collection);
            $this->_productCollections['brands_products'] = $collection;
        }
		return $collection;
	}

}