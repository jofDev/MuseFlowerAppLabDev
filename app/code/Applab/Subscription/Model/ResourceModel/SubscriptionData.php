<?php
  
namespace Applab\Subscription\Model\ResourceModel;
  
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
  
class SubscriptionData extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('applab_subscriptions', 'id');
    }
}
