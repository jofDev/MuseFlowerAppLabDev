<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Applab\CustomShipping\Model\ResourceModel;
/**
 * Description of City
 *
 * @author dharmendra
 */
class Area extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(){
    	$this->_init('applab_shipping_area','area_id');
    }
}