<?php

namespace Applab\Brands\Block\Brand;

class View extends \Magento\Framework\View\Element\Template
{
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
	protected $_coreRegistry = null;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_brandHelper;

    protected $_groupModel;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context       
     * @param \Magento\Catalog\Model\Layer\Resolver            $layerResolver 
     * @param \Magento\Framework\Registry                      $registry      
     * @param \Ves\Brand\Helper\Data                           $brandHelper   
     * @param \Ves\Brand\Model\Group                           $groupModel    
     * @param array                                            $data          
     */
    public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
    	\Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,      
    	\Magento\Framework\Registry $registry,
    	\Ves\Brand\Helper\Data $brandHelper,
        \Ves\Brand\Model\Group $groupModel,
        array $data = []
        ) {
    	$this->_brandHelper = $brandHelper;
    	$this->_catalogLayer = $layerResolver->get();
    	$this->_coreRegistry = $registry;
        $this->_groupModel = $groupModel;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Magento\Cms\Model\Page $brand
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $brandRoute = $this->_brandHelper->getConfig('general_settings/route');
        $brandRoute = $brandRoute?$brandRoute:"vesbrand/index/index";
        $page_title = $this->_brandHelper->getConfig('brand_list_page/page_title');
        $brand = $this->getCurrentBrand();

        $group = false;
        if($groupId = $brand->getGroupId()){
            $group = $this->_groupModel->load($groupId);
        }
        if($breadcrumbsBlock)
        {
        $breadcrumbsBlock->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link' => $baseUrl
            ]
            );
        
        $breadcrumbsBlock->addCrumb(
            'vesbrand',
            [
                'label' => $page_title,
                'title' => $page_title,
                'link' => $baseUrl.$brandRoute
            ]
            );
        
        if($this->getStoreCode() == 'ar'){

            $breadcrumbsBlock->addCrumb(
                'brand',
                [
                    'label' => __($brand->getNameAr()),
                    'title' => __($brand->getNameAr()),
                    'link' => ''
                ]
                );
            }
        else{
            $breadcrumbsBlock->addCrumb(
                'brand',
                [
                    'label' => __($brand->getName()),
                    'title' => __($brand->getName()),
                    'link' => ''
                ]
                );
            }

        }
    }

    public function getCurrentBrand()
    {
        $brand = $this->_coreRegistry->registry('current_brand');
        if ($brand) {
            $this->setData('current_brand', $brand);
        }
        return $brand;
    }

    /**
     * @return string
     */
    public function getProductListHtml()
    {
    	return $this->getChildHtml('product_list');
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $brand = $this->getCurrentBrand();
        $page_title = $brand->getName();
        $meta_description = $brand->getMetaDescription();
        $meta_keywords = $brand->getMetaKeywords();
        $this->_addBreadcrumbs();
        if($page_title){
            $this->pageConfig->getTitle()->set($page_title);   
        }
        if($meta_keywords){
            $this->pageConfig->setKeywords($meta_keywords);   
        }
        if($meta_description){
            $this->pageConfig->setDescription($meta_description);   
        }
        return parent::_prepareLayout();
    }
     /**
     * Get Store code
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
}