<?php
namespace Vnecoms\Sms\Controller\Geoip;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use GeoIp2\Database\Reader;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Index extends \Magento\Framework\App\Action\Action implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * @param Context $context
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        \Vnecoms\Sms\Helper\Data $helper,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }
    
    
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $reader = new Reader($this->helper->getGeoIpDatabase());
        $countryCode = '';
        try{
            $record = $reader->country($this->getClientIp());
            /* $record = $reader->country('149.56.130.117'); */
            $countryCode = $record->country->isoCode;
        }catch(\Exception $e){
            
        }

        /* If the GEO country is not in the allowed list then return default country code*/
        if(!$this->helper->isAllowedAllCountries()){
            $allowedCountries = $this->helper->getAllowedCountries();
            $allowedCountries = explode(",", $allowedCountries);
            if(!in_array($countryCode, $allowedCountries)){
                $countryCode = '';
            }
        }
        if(!$countryCode) {
            /* If the GEOIP can not detect the country code then use default country code. */
            /** @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig */
            $scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
            /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
            $storeManager = $this->_objectManager->create('Magento\Store\Model\StoreManagerInterface');

            $countryCode = $scopeConfig->getValue('general/country/default', 'store', $storeManager->getStore()->getId());
        }
        $response->setData(['country' => $countryCode]);
        if($callBack = $this->getRequest()->getParam('callback')){
            $this->getResponse()->setHeader('Content-Type', 'text/javascript');
            $content = '/**/typeof '.$callBack.' === \'function\' && '.$callBack.'('.$response->toJson().')';
            return $this->getResponse()->setBody($content);
        }
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
    
    /**
     * Get client ip
     * 
     * @return string
     */
    protected function getClientIp(){
        $params = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        $ip = '';
        foreach($params as $param){
            $ip = $this->getRequest()->getServer($param);
            if($ip) break;
        }
        return $ip;
        
    }
}
