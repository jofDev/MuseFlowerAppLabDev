<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Applab\CustomShipping\Model;
/**
 * Description of City
 *
 * @author dharmendra
 */
class Area extends \Magento\Framework\Model\AbstractModel
{
    const CACHE_TAG = 'applab_country_area';

    protected $_cacheTag = 'applab_country_area';

    protected $_eventPrefix = 'applab_country_area';
    
    /**
     * Initialize customer model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Applab\CustomShipping\Model\ResourceModel\Area');
    }
}
