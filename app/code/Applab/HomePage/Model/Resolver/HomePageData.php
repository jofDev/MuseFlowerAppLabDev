<?php
namespace Applab\HomePage\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

use Mageplaza\BannerSlider\Model\ResourceModel\Slider\Collection as sliderCollection;
use Mageplaza\BannerSlider\Model\ResourceModel\Slider\CollectionFactory as sliderCollectionFactory;
use Mageplaza\BannerSlider\Model\ResourceModel\Banner\Collection  as bannerCollection;
use Mageplaza\BannerSlider\Model\ResourceModel\Banner\CollectionFactory as bannerCollectionFactory;
use Ves\Brand\Model\ResourceModel\Brand\CollectionFactory as brandCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as categoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as productCollectionFactory;
use Magento\Store\Model\StoreManagerInterface as storeManager;
use Magento\Framework\App\Config\ScopeConfigInterface as scopeConfig;

class HomePageData implements ResolverInterface
{
   
    public function __construct(
        sliderCollectionFactory $sliderCollectionFactory,
        bannerCollectionFactory $bannerCollectionFactory,
        brandCollectionFactory $brandFactory,
        categoryCollectionFactory $categoryCollectionFactory,
        productCollectionFactory $productCollectionFactory,
        storeManager $storeManager,
        scopeConfig $scopeConfig

    )
    {
        $this->sliderCollectionFactory      = $sliderCollectionFactory; 
        $this->bannerCollectionFactory      = $bannerCollectionFactory;   
        $this->brandFactory                 = $brandFactory;  
        $this->categoryCollectionFactory    = $categoryCollectionFactory;  
        $this->productCollectionFactory     = $productCollectionFactory;  
        $this->storeManager                 = $storeManager;  
        $this->scopeConfig                  = $scopeConfig;  
    }
    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null)
    {
        if (!isset($args['store_id']))
        {
            throw new GraphQlInputException(__('Id required. Stroe Id is required!'));
        }
        try {

            $banners            = $designers = $occassional = $featured = [];
            $storeId            = $args['store_id'];
            $mediaUrl           = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
            $placeholder        = $this->scopeConfig->getValue('catalog/placeholder/image_placeholder'); 
            $placeholderImage   = $placeholder ? $mediaUrl.'catalog/product/placeholder/'.$placeholder : '';
            $occasionalCategory = $this->scopeConfig->getValue('adminsettings_section/general/occasional_cat');

            // banners
            $sliderCol          = $this->sliderCollectionFactory->create();
            $sliderCol->getSelect()->join(
                    ['banner_slider' => $sliderCol->getTable('mageplaza_bannerslider_banner_slider')],
                    'main_table.slider_id=banner_slider.slider_id AND main_table.status = 1 AND main_table.store_ids ='.$storeId,
                    ['banner_id']
            );
            $slider_banners = $sliderCol->getData();
            if(count($slider_banners) > 0 ){
                $bannerIds = [];
                foreach ($slider_banners as $value) {
                    $bannerIds[] = $value['banner_id'];
                }

                $bannerCol = $this->bannerCollectionFactory->create();
                $bannerCol->addFieldToSelect('*');
                $bannerCol->addFieldToFilter('banner_id', ['IN' => $bannerIds]);
                if(count($bannerCol->getData()) > 0 ){
                    foreach ($bannerCol->getData() as  $banner) {
                        $bannerImage =  $placeholderImage ;
                        if($banner['image']){
                            $bannerImage  = $mediaUrl.'mageplaza/bannerslider/banner/image/'.$banner['image'];
                        }
                        $banners[] = ['name' => $banner['name'], 'image' => $bannerImage];
                    }
                }
            }
            // designers
            $brandCol  = $this->brandFactory->create();
            $brandCol->getSelect()->join(
                    ['brand_store' => $brandCol->getTable('ves_brand_store')],
                    'main_table.brand_id = brand_store.brand_id AND main_table.status = 1 AND brand_store.store_id ='.$storeId,
                    []
            );
            if(count($brandCol->getData()) > 0 ){
                foreach ($brandCol->getData() as  $brand) {
                    $brandImage =  $placeholderImage ;
                    if($brand['image']){
                        $brandImage  = $mediaUrl.$brand['image'];
                    }
                    $designers[] = ['name' => $brand['name'], 'name_ar' =>$brand['name_ar'], 'status' =>$brand['status'], 'image' => $brandImage];
                }
            }
            // occasional 
            $categoryIds = $occasionalCategory ? explode(",", $occasionalCategory) : [];
            if(count($categoryIds) > 0 ){
                $categoryCol = $this->categoryCollectionFactory->create();
                $categoryCol->addFieldToSelect('*');
                $categoryCol->addFieldToFilter('entity_id', ['IN' => $categoryIds])->setStoreId($storeId);
                foreach ($categoryCol as $category) {
                    $catImage  = $placeholderImage ;
                    if($category->getImage()){
                        $url = explode("/", $category->getImage(), 3);
                        $catImage  = isset($url[2]) ? $mediaUrl.$url[2] : '';
                    } 
                    $occassional[] = ['name' => $category->getName(), 'image' => $catImage];
                }
            }
            // featured
            $productCol = $this->productCollectionFactory->create();
            $productCol->addAttributeToSelect('*')
                       ->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                       ->addStoreFilter($storeId)
                       ->addAttributeToFilter('featured_product', '1');

            if($productCol->getSize() > 0 ){
                $proImage  = $placeholderImage ;
                foreach ($productCol as $product) {
                    if($product->getImage()){
                         $proImage  = $mediaUrl.'catalog/product'.$product->getImage();
                    }
                    $featured[] = ['name' => $product->getName(), 'image' => $proImage];
                }
            }

            return ['banners' => $banners, 'designers'  => $designers, 'occassional'  => $occassional, 'featured'  => $featured];
       
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return fasle;
    }
}