<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Core\Model\ResourceModel\Key;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * App page collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'key_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Vnecoms\Core\Model\Key', 'Vnecoms\Core\Model\ResourceModel\Key');
    }

}
