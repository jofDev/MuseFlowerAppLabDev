<?php
namespace Applab\Subscription\Model;
  
use Magento\Framework\Model\AbstractModel;
  
class SubscriptionData extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Applab\Subscription\Model\ResourceModel\SubscriptionData');
    }
}
