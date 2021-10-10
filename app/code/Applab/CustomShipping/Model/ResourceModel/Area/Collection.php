<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Applab\CustomShipping\Model\ResourceModel\Area;
/**
 * Description of Collection
 *
 * @author dharmendra
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     *
     * @var type 
     */
    protected $_idFieldName = 'area_id';
    
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
            $this->_init('Applab\CustomShipping\Model\Area', 'Applab\CustomShipping\Model\ResourceModel\Area');
            $this->_map['fields']['area_id'] = 'main_table.area_id';
    }
}