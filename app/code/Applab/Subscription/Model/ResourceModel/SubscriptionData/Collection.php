<?php
  
namespace Applab\Subscription\Model\ResourceModel\SubscriptionData;
  
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
  
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Applab\Subscription\Model\SubscriptionData',
            'Applab\Subscription\Model\ResourceModel\SubscriptionData'
        );
    }
}
