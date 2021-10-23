<?php
  
namespace Applab\EventsPro\Model\ResourceModel;
  
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
  
class EventsProRegistration extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('applab_eventspro_registration', 'id');
    }
}
