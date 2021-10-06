<?php
namespace Vnecoms\Sms\Model\ResourceModel\Mobile;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'mobile_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Sms\Model\Mobile', 'Vnecoms\Sms\Model\ResourceModel\Mobile');
    }

}
