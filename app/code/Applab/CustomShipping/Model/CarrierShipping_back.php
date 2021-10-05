<?php
namespace Applab\CustomShipping\Model;

use Magento\Quote\Model\Quote\Address\RateResult\Error;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Simplexml\Element;
use Magento\Ups\Helper\Config;
use Magento\Framework\Xml\Security;


class CarrierShipping extends AbstractCarrierOnline implements CarrierInterface
{

    /**
     * @var \Applab\CustomShipping\Helper\Data
     */
    protected $helper;

    const CODE = 'custom_shipping';
    protected $_code = self::CODE;
    protected $_request;
    protected $_result;
    protected $_baseCurrencyRate;
    protected $_xmlAccessRequest;
    protected $_localeFormat;
    protected $_logger;
    protected $configHelper;
    protected $_errors = [];
    protected $_isFixed = true;
    protected $_checkoutSession;
    protected $_productloader;
    
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Applab\CustomShipping\Helper\Data $helper,
        \Applab\CustomShipping\Model\Source\Method $customMethod,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $_checkoutSession,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        Config $configHelper,
        array $data = []
    ) {
        $this->_localeFormat = $localeFormat;
        $this->configHelper = $configHelper;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
        $this->helper = $helper;
        $this->attributeRepository = $attributeRepository;
        $this->customMethod = $customMethod;
        $this->timezone = $timezone;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $_checkoutSession; 
        $this->_productloader = $_productloader;
    }

    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * Do shipment request to carrier web service, obtain Print Shipping Labels and process errors in response
     *
     * @param \Magento\Framework\DataObject $request
     * @return \Magento\Framework\DataObject
     */
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $result = new \Magento\Framework\DataObject();
        $result->setShippingLabelContent('Shipping Label Content');
        $result->setTrackingNumber('12342342342');

        return $result;
    }

    /**
     * Do request to shipment
     *
     * @param Request $request
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function requestToShipment($request)
    {
        $result = $this->_doShipmentRequest($request);

        $response = new \Magento\Framework\DataObject(
            [
                'info' => [
                    [
                        'tracking_number' => $result->getTrackingNumber(),
                        'label_content' => $result->getShippingLabelContent(),
                    ],
                ],
            ]
        );

        return $response;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        $configMethods = $this->getAllowedMethodsConfigValue();
        $allowedMethods = array();
        if(!empty($configMethods)) {
            $methods = explode(',', $configMethods);
            $methodTitle = $this->customMethod->toArray();
            
            foreach($methods as $method) {
                if($method == 'regular_shipping' && isset($methodTitle[$method])) {
                    $allowedMethods[] = ['value' => $method, 'label' => $methodTitle[$method],  'description' => $this->getRegularShippingDescriptionConfigValue()];
                }
                
            }
        } 
        
        return $allowedMethods;

    }

    /**
     * Do request to shipment
     *
     * @param Request $request
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    
    public function collectRates(RateRequest $request)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_checkout.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        
        $regionId =  $request->getDestRegionId();
        $regionName =  $request->getDestRegionName();


        $cartData = array();
        $cartData = $this->_checkoutSession->getQuote()->getAllVisibleItems();
        $cartDataCount = count( $cartData );
        $logger->info('user cartDataCount : '.$cartDataCount);
        $freeShippingFlag =0;
        $manufacturerArray = array();
        $regularPrice = 50;
       
            foreach( $cartData as $item ): 
                $product = $item->getProduct();
                $productData = $this->_productloader->create()->load($product->getId());
                // $logger->info('product -:'.$productData->getId()); 
                $manufacturer = $productData->getData('manufacturer');
                //  $logger->info('manufacturer -:'.$manufacturer); 
                $manufacturerArray[] = $manufacturer ;
            endforeach; 
      
        $uniqueManufacture = array_unique($manufacturerArray);
        $uniqueManufactureCount = count($uniqueManufacture);
       //$logger->info('user uniqueManufacture : '.print_r($uniqueManufacture));
         $logger->info('user uniqueManufacture count : '.count($uniqueManufacture));
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $result = $this->_rateFactory->create();        
        $isInRemoteArea = 0;
        $resultShipping = $this->getRegionInShipping($regionId);
        $logger->info('resultShipping-:'.print_r($resultShipping,1));
        if(!empty($resultShipping)){

             $isInRemoteArea  = $resultShipping['isInRemorteArea'];
                if($isInRemoteArea == 1){
                    $regularPrice = $this->getRegularShippingPriceRemorte();
                }else{
                    $regularPrice = $this->getRegularShippingPriceNonRemorte();
                }
        }else{
            $regularPrice = $this->getRegularShippingPriceNonRemorte();
        }
       
        $qatarTimeZone = $this->helper->getTimezonByCountryAndRegion('QA', 0);
        
        $qatarTime = $this->converToTz($qatarTimeZone, $this->timezone->getConfigTimezone());

        foreach($this->getAllowedMethods() as $customMethod) {
            //if (($DateRange['PostQopen'] <= $now) && ($DateRange['PostQClose'] > $now)) {

            $method = $this->_rateMethodFactory->create();
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));
     
            $method->setMethod($customMethod['value']);
            $method->setMethodTitle(__($customMethod['label'])."  ".$customMethod['description']);

            
            $amount = $uniqueManufactureCount * $regularPrice;
            $method->setPrice($amount);
            $method->setCost($amount);
            $result->append($method);
            
        }
        return $result; 
    }
    
    public function proccessAdditionalValidation(\Magento\Framework\DataObject $request) {
        return true;
    }
  

    public function getAllowedMethodsConfigValue()
    {
         $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue('carriers/custom_shipping/allowed_methods', $storeScope);
    }


    public function getRegionInShipping($regionId)
    {    
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_shipping.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('getRegionInShipping -:'.$regionId); 
        $resultShipping = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $areas = $objectManager->create('Applab\CustomShipping\Model\Area')->getCollection();
        $areas->addFieldToFilter('area_zone_id', ['eq' => $regionId]);   
       
        $logger->info('count -:'.$areas->count()); 
        if($areas->count() > 0) {
            $logger->info('count -:'.$areas->getFirstItem()->getIsInRemorteArea()); 
            $resultShipping['isInRemorteArea'] = $areas->getFirstItem()->getIsInRemorteArea();
            return $resultShipping;
        }else{
            return $resultShipping;
        }
    }



    /**
     * converToTz convert Datetime from one zone to another
     * @param string $dateTime which we want to convert
     * @param string $toTz timezone in which we want to convert
     * @param string $fromTz timezone from which we want to convert
    */
    protected function converToTz($toTz='', $fromTz='')
    {
        $dateTime = date('H:i');        
        $date = new \DateTime($dateTime, new \DateTimeZone($fromTz));
        $date->setTimezone(new \DateTimeZone($toTz));
        $dateTime = $date->format('H:i:s');
        return $dateTime;
    }

    protected function converToTzConf($dateTime, $toTz='', $fromTz='')
    {
        $dateTime = str_replace(',', ':', $dateTime);
          
        //$date = new \DateTime($dateTime, new \DateTimeZone($fromTz));
        //$date->setTimezone(new \DateTimeZone($toTz));
       //$dateTime = $date->format('H:i:s');
        return $dateTime;
    }


    public function getRegularShippingDescriptionConfigValue()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue('carriers/custom_shipping/regular_shipping_description', $storeScope);
    }

    public function getRegularShippingPriceNonRemorte()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue('carriers/custom_shipping/regular_shipping_price_nonremorte', $storeScope);
    }
    public function getRegularShippingPriceRemorte()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue('carriers/custom_shipping/regular_shipping_price_remorte', $storeScope);
    }

    public function dateIsBetween($from, $to, $date="now") {
        $date = new \DateTime($date);
 
        $from= new \DateTime($from);
        $to = new \DateTime($to);
        if ($date >= $from && $date <= $to) {
            return true;
        }
        return false;
    }
}