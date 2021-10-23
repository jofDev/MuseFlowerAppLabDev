<?php
namespace Applab\HomePage\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

use Mageplaza\BannerSlider\Model\ResourceModel\Slider\CollectionFactory as sliderCollectionFactory;
use Ves\Brand\Model\ResourceModel\Brand\CollectionFactory as brandCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as categoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as productCollectionFactory;
use Magento\Store\Model\StoreManagerInterface as storeManager;
use Magento\Framework\App\Config\ScopeConfigInterface as scopeConfig;

use DecimaDigital\PhotoGallery\Model\ResourceModel\Gallery\CollectionFactory  as galleryCollectionFactory;

class HomePageData implements ResolverInterface
{
   
    public function __construct(
        sliderCollectionFactory $sliderCollectionFactory,
        brandCollectionFactory $brandFactory,
        categoryCollectionFactory $categoryCollectionFactory,
        productCollectionFactory $productCollectionFactory,
        storeManager $storeManager,
        scopeConfig $scopeConfig,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attrSetCollectionFactory,
        galleryCollectionFactory $galleryCollectionFactory
    )
    {
        $this->sliderCollectionFactory      = $sliderCollectionFactory;   
        $this->brandFactory                 = $brandFactory;  
        $this->categoryCollectionFactory    = $categoryCollectionFactory;  
        $this->productCollectionFactory     = $productCollectionFactory;  
        $this->storeManager                 = $storeManager;  
        $this->scopeConfig                  = $scopeConfig;  
        $this->attrSetCollectionFactory     = $attrSetCollectionFactory;
        $this->galleryCollectionFactory     = $galleryCollectionFactory;
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
        if (count($args) != 7)
        {
            throw new GraphQlInputException(__('Required inputs are missing!'));
        }
        try {
            $banners            = $designers = $occassional = $featured = $gift = $tmp = $vip = [];
            $storeId            = $args['store_id'];
            $limit              = 10;

            $mediaUrl           = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
            $placeholder        = $this->scopeConfig->getValue('catalog/placeholder/image_placeholder'); 
            $placeholderImage   = $placeholder ? $mediaUrl.'catalog/product/placeholder/'.$placeholder : '';
            $occasionalCategory = $this->scopeConfig->getValue('adminsettings_section/general/occasional_cat');

            if($args['banners'] == 1 || $args['banners'] == '-1'){
                // banners
                $sliderCol          = $this->sliderCollectionFactory->create();
                $sliderCol->getSelect()->join(
                        ['banner_slider' => $sliderCol->getTable('mageplaza_bannerslider_banner_slider')],
                        'main_table.slider_id=banner_slider.slider_id AND main_table.status = 1 AND main_table.store_ids ='.$storeId,
                        []
                );
                $sliderCol->getSelect()->join(
                        ['slider_banner' => $sliderCol->getTable('mageplaza_bannerslider_banner')],
                        'banner_slider.banner_id = slider_banner.banner_id AND slider_banner.status = 1',
                        ['name','image']
                );
                if($args['banners'] == 1){           
                    $sliderCol->getSelect()->limit($limit);
                } 
                if(count($sliderCol->getData()) > 0 ){
                        foreach ($sliderCol->getData() as  $banner) {
                            $bannerImage =  $placeholderImage ;
                            if($banner['image']){
                                $bannerImage  = $mediaUrl.'mageplaza/bannerslider/banner/image/'.$banner['image'];
                            }
                            $banners[] = ['name' => $banner['name'], 'image' => $bannerImage];
                        }
                }
            }
            // designers
            if($args['designers'] == 1 || $args['designers'] == '-1'){
                $brandCol  = $this->brandFactory->create();
                $brandCol->getSelect()->join(
                        ['brand_store' => $brandCol->getTable('ves_brand_store')],
                        'main_table.brand_id = brand_store.brand_id AND main_table.status = 1 AND brand_store.store_id ='.$storeId,
                        []
                );
                if($args['designers'] == 1){           
                    $brandCol->getSelect()->limit($limit);
                }

                if(count($brandCol->getData()) > 0 ){
                    foreach ($brandCol->getData() as  $brand) {
                        $brandImage =  $placeholderImage ;
                        if($brand['image']){
                            $brandImage  = $mediaUrl.$brand['image'];
                        }
                        $designers[] = ['name' => $brand['name'], 'name_ar' =>$brand['name_ar'], 'status' =>$brand['status'], 'image' => $brandImage];
                    }
                }
            }
            // occasional 
            if($args['occassional'] == 1 || $args['occassional'] == '-1'){
                $categoryIds = $occasionalCategory ? explode(",", $occasionalCategory) : [];
                if(count($categoryIds) > 0 ){
                    $categoryCol = $this->categoryCollectionFactory->create();
                    $categoryCol->addAttributeToSelect('name')->addAttributeToSelect('image');
                    $categoryCol->addFieldToFilter('entity_id', ['IN' => $categoryIds])->setStoreId($storeId);

                    if($args['occassional'] == 1){           
                        $categoryCol->getSelect()->limit($limit);
                    }

                    foreach ($categoryCol as $category) {
                        $catImage  = $placeholderImage ;
                        if($category->getImage()){
                            $url = explode("/", $category->getImage(), 3);
                            $catImage  = isset($url[2]) ? $mediaUrl.$url[2] : '';
                        } 
                        $occassional[] = ['name' => $category->getName(), 'image' => $catImage];
                    }
                }
            }
            // featured
            if($args['featured'] == 1 || $args['featured'] == '-1'){
                $productCol = $this->productCollectionFactory->create();
                $productCol->addAttributeToSelect('*')
                           ->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                           ->addStoreFilter($storeId)
                           ->addAttributeToFilter('featured_product', '1');
                if($args['featured'] == 1){           
                    $productCol->getSelect()->orderRand()->limit($limit);
                } else {
                    $productCol->getSelect()->orderRand();
                }
                if($productCol->getSize() > 0 ){
                    $proImage  = $placeholderImage ;
                    foreach ($productCol as $product) {
                        if($product->getImage()){
                             $proImage  = $mediaUrl.'catalog/product'.$product->getImage();
                        }
                        $featured[] = ['name' => $product->getName(), 'sku' => $product->getSku(), 'image' => $proImage];
                    }
                }
            }

            // gift 
            if($args['gift'] == 1 || $args['gift'] == '-1'){
                $attribute_set_collection = $this->attrSetCollectionFactory->create();
                $attribute_set_collection->addFieldToFilter('entity_type_id',4)->addFieldToFilter('attribute_set_name','Gift Product');

                if(count($attribute_set_collection->getData()) > 0 ){
                    $att_set    = current($attribute_set_collection->getData()); 
                    $att_set_id = $att_set["attribute_set_id"];
                    $giftCol    = $this->productCollectionFactory->create();
                    $giftCol->addAttributeToSelect('*')
                            ->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                            ->addStoreFilter($storeId)
                            ->addFieldToFilter('attribute_set_id', $att_set_id);

                    if($args['gift'] == 1){           
                        $giftCol->getSelect()->orderRand()->limit($limit);
                    } else {
                        $giftCol->getSelect()->orderRand();
                    }

                    if($giftCol->getSize() > 0 ){
                        $giftproImage  = $placeholderImage ;
                        foreach ($giftCol as $product) {
                            if($product->getImage()){
                                 $giftproImage  = $mediaUrl.'catalog/product'.$product->getImage();
                            }
                            $gift[] = ['name' => $product->getName(), 'sku' => $product->getSku(), 'image' => $giftproImage];
                        }
                    }
                }
            }

            // VIP
            if($args['vip'] == 1 || $args['vip'] == '-1' ){
                $galleryCol = $this->galleryCollectionFactory->create();
                $galleryCol->getSelect()->join(
                        ['gallery_relation' => $galleryCol->getTable('decimadigital_photogallery_relation')],
                        'main_table.gallery_id=gallery_relation.gallery_id AND main_table.status = 1 AND main_table.store_id ='.$storeId,
                        ['photo_id']
                );
                $galleryCol->getSelect()->join(
                        ['gallery_photos' => $galleryCol->getTable('decimadigital_photogallery_photos')],
                        'gallery_relation.photo_id=gallery_photos.id AND gallery_photos.status = 1',
                        ['url','id']
                ); 
                if($args['vip'] == 1){           
                        $galleryCol->getSelect()->limit($limit);
                }           
       
                foreach($galleryCol->getData() as $data)
                {
                    $photos[$data['gallery_id']][$data['id']]        = $data['url'] ? $mediaUrl.'photo_gallery'.$data['url'] : $placeholderImage;
                    $tmp[$data['gallery_id']]['name']                = $data['name'];
                    $tmp[$data['gallery_id']]['gallery']             = $photos;
                    $tmp[$data['gallery_id']]['default_photo_id']    = $photos[$data['gallery_id']][$data['default_photo_id']];
                }
                   
                foreach($tmp as $gallery_id => $values)
                {
                    if(($key = array_search($values['default_photo_id'], $values['gallery'][$gallery_id])) !== false) {
                        unset($values['gallery'][$gallery_id][$key]); // remove main photo from gallery
                    }
                    $gallery = count($values['gallery'][$gallery_id]) > 0 ? array_values($values['gallery'][$gallery_id])  : null;
                    $vip[]   = [
                                'name' => $values['name'],
                                'logo' => $values['default_photo_id'],
                                'gallery' => $gallery 
                            ];
                }
            }
            //result
            return ['banners' => $banners, 'designers'  => $designers, 'occassional'  => $occassional, 'featured'  => $featured, 'gift'  => $gift, 'vip'  => $vip];
       
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return fasle;
    }
}