<?php

namespace Applab\Brands\Block\Brand;

class ListSidebar extends \Magento\Framework\View\Element\Template
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


    protected $_collection;

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
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        if(!$this->getConfig('general_settings/enable')) return;
        parent::_construct();

        $store = $this->_storeManager->getStore();
        $itemsperpage = (int)$this->getConfig('brand_list_page/item_per_page',12);
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
        if($this->_collection == null){
            $store = $this->_storeManager->getStore();
            $brand = $this->_brand;
            $brandCollection = $brand->getCollection()
            ->addFieldToFilter('status',1)
            ->addStoreFilter($store)
            ->setOrder('position','ASC');

            $brandCollection->getSelect()->reset(\Zend_Db_Select::ORDER);
            $brandCollection->setOrder('position','ASC');
            $this->setCollection($brandCollection);
        }
        return $this->_collection;
    }
}