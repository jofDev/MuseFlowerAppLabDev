<?php
  
namespace Applab\EventsPro\Model\ResourceModel\EventsProData;
  
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
  
class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Applab\EventsPro\Model\EventsProData',
            'Applab\EventsPro\Model\ResourceModel\EventsProData'
        );
    }
}
