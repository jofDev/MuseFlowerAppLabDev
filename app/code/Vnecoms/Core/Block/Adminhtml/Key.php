<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Core\Block\Adminhtml;

class Key extends \Magento\Backend\Block\Widget\Grid\Container
{    
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Vnecoms_Core';
        $this->_controller = 'adminhtml';
        $this->_headerText = __('License Key');
        $this->_addButtonLabel = __('Add new License Key');
        parent::_construct();
    }
}