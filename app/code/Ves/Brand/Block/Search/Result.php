<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Brand\Block\Search;
use Magento\Customer\Model\Context as CustomerContext;

class Result extends \Magento\Framework\View\Element\Template
{

	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Ves\Brand\Helper\Data
     */
    protected $_brandHelper;

    /**
     * @var \Ves\Brand\Model\Brand
     */
    protected $_brand;
    protected $_request;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context      
     * @param \Magento\Framework\Registry                      $registry     
     * @param \Ves\Brand\Helper\Data                           $brandHelper  
     * @param \Ves\Brand\Model\Brand                           $brand        
     * @param \Magento\Store\Model\StoreManagerInterface       $storeManager 
     * @param array                                            $data         
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\Brand\Helper\Data $brandHelper,
        \Ves\Brand\Model\Brand $brand,
        array $data = []
        ) {
        $this->_brand = $brand;
        $this->_coreRegistry = $registry;
        $this->_brandHelper = $brandHelper;
        $this->_request      = $context->getRequest();
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if(!$this->getConfig('general_settings/enable')) return;
        parent::_construct();
        $template = '';
        $layout = $this->getConfig('brand_list_page/layout');
        if($layout == 'grid'){
            $template = 'brandlistpage_grid.phtml';
        }else{
            $template = 'brandlistpage_list.phtml';
        }
        if(!$this->hasData('template')){
            $this->setTemplate($template);
        }
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
        $page_title = $this->_brandHelper->getConfig('brand_list_page/page_title');

        if($breadcrumbsBlock){

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
            'link' => $baseUrl.'/'.$brandRoute
            ]
            );
        $breadcrumbsBlock->addCrumb(
            'vesbrandsearch',
            [
            'label' => __("Search Brand Result"),
            'title' => __("Search Brand Result"),
            'link' => ''
            ]
            );
        }
    }

    /**
     * Set brand collection
     * @param \Ves\Brand\Model\Brand
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    /**
     * Retrive brand collection
     * @param \Ves\Brand\Model\Brand
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    public function getConfig($key, $default = '')
    {
        $result = $this->_brandHelper->getConfig($key);
        if(!$result){

            return $default;
        }
        return $result;
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $page_title = $this->getConfig('brand_list_page/page_title');
        $meta_description = $this->getConfig('brand_list_page/meta_description');
        $meta_keywords = $this->getConfig('brand_list_page/meta_keywords');
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('ves-brandlist');
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
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('vesbrand_toolbar');
        if ($block) {
            $block->setDefaultOrder("position");
            $block->removeOrderFromAvailableOrders("price");
            return $block;
        }
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $itemsperpage = (int)$this->getConfig('brand_list_page/item_per_page',12);
        $store = $this->_storeManager->getStore();
        $brand = $this->_brand;
        $brandCollection = $brand->getCollection()
        ->addFieldToFilter('status',1)
        ->addStoreFilter($store)
        ->setOrder('position','ASC');
        $this->setCollection($brandCollection);

        $searchKey = $this->_request->getParam('s');

        $brandCollection->addFieldToFilter(['name', 'description', 'url_key'], [
                                    ['like'=>'%'.addslashes($searchKey).'%'],
                                    ['like'=>'%'.addslashes($searchKey).'%'],
                                    ['like'=>'%'.addslashes($searchKey).'%']
                            ])
                        ->setPageSize($itemsperpage)
                        ->setCurPage(1);

        $this->setCollection($brandCollection);

        $toolbar = $this->getToolbarBlock();

        // set collection to toolbar and apply sort
        if($toolbar){
            $itemsperpage = (int)$this->getConfig('brand_list_page/item_per_page',12);
            $toolbar->setData('_current_limit',$itemsperpage)->setCollection($brandCollection);
            $this->setChild('toolbar', $toolbar);
        }
        return parent::_beforeToHtml();
    }
}