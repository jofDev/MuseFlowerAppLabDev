<?php
namespace Vnecoms\Sms\Model\ResourceModel;

/**
 * Mobile mysql resource
 */
class Mobile extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_sms_customer_mobile', 'mobile_id');
    }
}
