<?php
namespace Vnecoms\Sms\Model\ResourceModel\Rule;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Rule collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'rule_id';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Sms\Model\Rule', 'Vnecoms\Sms\Model\ResourceModel\Rule');
    }

}
