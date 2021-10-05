<?php
namespace Vnecoms\Sms\Model\ResourceModel;

use Magento\Customer\Model\CustomerFactory;
use Magento\Store\Model\StoreManagerInterface;

class CustomerRepository
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @param CustomerFactory $customerFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CustomerFactory $customerFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
    }
    
    
    /**
     * Login by mobile
     * 
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $subject
     * @param \Closure $proceed
     * @param string $email
     * @param string $websiteId
     * @return Ambigous <\Magento\Customer\Model\Customer, \Magento\Framework\mixed>
     */
    public function aroundGet(
        \Magento\Customer\Api\CustomerRepositoryInterface $subject,
        \Closure $proceed,
        $email,
        $websiteId = null
    ) {
        $customer = $this->customerFactory->create();
        if ($websiteId === null) {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $customer->setWebsiteId($websiteId);
        }
        
        $resource = $customer->getResource();
        
        $connection = $resource->getConnection();
        $bind = ['mobilenumber' => $email];
        $select = $connection->select()->from(
            $resource->getEntityTable(),
            [$resource->getEntityIdField()]
        )->where(
            'mobilenumber = :mobilenumber'
        );
        
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            if (!$customer->hasData('website_id')) {
                return $proceed($email, $websiteId);
            }
            $bind['website_id'] = (int)$customer->getWebsiteId();
            $select->where('website_id = :website_id');
        }

        $customerId = $connection->fetchOne($select, $bind);
        if ($customerId) {
            $resource->load($customer, $customerId);
            if (!$customer->getEmail()) {
                return $proceed($email, $websiteId);
            }
            return $customer->getDataModel();
        }

        return $proceed($email, $websiteId);
    }
}
