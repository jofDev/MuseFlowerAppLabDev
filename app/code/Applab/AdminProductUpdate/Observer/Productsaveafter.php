<?php

    namespace Applab\AdminProductUpdate\Observer;

    use Magento\Framework\Event\ObserverInterface;

    class Productsaveafter implements ObserverInterface
    {

        /**
         * @var  \Magento\Catalog\Api\CategoryLinkManagementInterface
         */
        protected $categoryLinkManagement;

        /**
         * @var      \Magento\Framework\Message\ManagerInterface
         */
        protected $messageManager;

        /**
         * @var
         */
        protected $productCollection;

         /**
          *  @var \Magento\Catalog\Helper\Product\Edit\Action\Attribute
          */
         protected $attributeHelper;


         protected $request;

        /**
         * Save constructor.
         * @param Action\Context $context
         * @param \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement
         * @param \Magento\Framework\Message\ManagerInterface $messageManager
         * @param \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper
         * @param \Magento\Framework\App\Request\Http $request
         */
     


        public function __construct(
            \Magento\Backend\App\Action\Context $context,
            \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
            \Magento\Framework\Message\ManagerInterface $messageManager,
            \Magento\Framework\App\Request\Http $request,
            \Magento\Catalog\Helper\Product\Edit\Action\Attribute $attributeHelper,
            \Magento\Catalog\Model\Product $productModel
        ) {
            $this->categoryLinkManagement = $categoryLinkManagement;
            $this->messageManager = $messageManager;
            $this->attributeHelper = $attributeHelper;
            $this->request = $request;
            $this->productModel = $productModel;
        }






         /**
          * @return \Magento\Framework\App\Request\Http
          */
         protected function getRequest()
         {
             return $this->request;
         }

     

        /**
         * @param array $categoryIds
         */
        public function addProductToCategory($categoryIds=[],$product){
            if(!count($categoryIds)){
                return;
            }

                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/products_add.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);

                 $categoryIdValues= $product->getCategoryIds();

                 
                 $logger->info('categoryIds : '.print_r($categoryIds,1));

                 $logger->info('addProductToCategory : '.print_r($categoryIdValues,1));
          
                $categoryIdsArray = array_unique(
                    array_merge($categoryIds, $product->getCategoryIds()),
                    SORT_STRING
                );

                $logger->info('categoryIdsArray : '.print_r($categoryIdsArray,1));


                $this->categoryLinkManagement->assignProductToCategories(
                    $product->getSku(),
                    $categoryIdsArray
                );

                 $logger->info('end');
            
        }

        /**
         * @param array $categoryIds
         */
        public function removeProductToCategory($categoryIds=[],$product){
            if(!count($categoryIds)){
                return;
            }
       
                $categoryIdsArray = array_diff($product->getCategoryIds(), $categoryIds);
                $this->categoryLinkManagement->assignProductToCategories(
                    $product->getSku(),
                    $categoryIdsArray
                );
           
        }

         /**
          * @param \Magento\Framework\Event\Observer $observer
          * @return mixed
          */
        public function execute(\Magento\Framework\Event\Observer $observer)
        {
           
            /* Collect Data */


            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/products.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $categoryRemoveData = [];
            $categoryAddData = [];
            $specialPrice ='';
            $swFeatured = 0;

            $product = $observer->getProduct();  // you will get product object
            $product_id = $product->getId();     // for product id
            $product_sku = $product->getSku();   // for sku


            $currentproduct = $this->productModel->load($product_id);
            $specialPrice = $currentproduct->getSpecialPrice();
              $logger->info('specialPrice : '.$specialPrice);
            $swFeatured = $currentproduct->getSwFeatured();
               $logger->info('swFeatured : '.$swFeatured);
            $categoryIdValues= $currentproduct->getCategoryIds();

            $logger->info('categoryIdValues : '.print_r($categoryIdValues,1));

            if($specialPrice && isset($specialPrice)){
                if(!in_array(30,$categoryIdValues)){
                    array_push($categoryAddData,30);
                    $logger->info('sp add push : 30');
                }

            }else{
                if(in_array(30,$categoryIdValues)){
                    array_push($categoryRemoveData,30);
                    $logger->info('sp remove push : 30');
                }

            }


             if($swFeatured && isset($swFeatured)){
                 if(!in_array(28,$categoryIdValues)){
                    array_push($categoryAddData,28);
                    $logger->info('fe add push : 28');
                }
            }else{
                if(in_array(28,$categoryIdValues)){
                    array_push($categoryRemoveData,28);
                    $logger->info('fe remove push : 28');
                  
                }
            }


          


            try {
                if (!empty($categoryAddData)
                    || !empty($categoryRemoveData)
                ) {
                    if ($categoryAddData) {
                        $this->addProductToCategory($categoryAddData,$currentproduct);
                        $addCat = "updated";
                        $logger->info('updated');
                    }
                    if ($categoryRemoveData) {
                        $this->removeProductToCategory($categoryRemoveData,$currentproduct);
                        $addCat = "Removed";
                        $logger->info('Removed');
                    }

                    $this->messageManager
                        ->addSuccess(__(
                            'Special Category is'.$addCat
                        ));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } 
        }
    }