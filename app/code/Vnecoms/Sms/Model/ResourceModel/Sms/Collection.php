<?php
namespace Vnecoms\Sms\Model\ResourceModel\Sms;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'message_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Sms\Model\Sms', 'Vnecoms\Sms\Model\ResourceModel\Sms');
    }

}
