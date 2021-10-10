<?php
namespace Vnecoms\Sms\Model\ResourceModel;

/**
 * Sms mysql resource
 */
class Sms extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_sms_message', 'message_id');
    }
}
