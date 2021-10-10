<?php
namespace Applab\EventsPro\Model;
  
use Magento\Framework\Model\AbstractModel;
  
class EventsProData extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Applab\EventsPro\Model\ResourceModel\EventsProData');
    }
}
