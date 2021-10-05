<?php
namespace Vnecoms\Sms\Model\ResourceModel;

/**
 * Sms block rule mysql resource
 */
class Rule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_sms_block_list', 'rule_id');
    }
}
