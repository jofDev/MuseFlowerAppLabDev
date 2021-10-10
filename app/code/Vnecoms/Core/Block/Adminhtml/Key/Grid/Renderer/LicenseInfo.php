<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Core\Block\Adminhtml\Key\Grid\Renderer;

use Magento\Framework\DataObject;

class LicenseInfo extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Vnecoms\Core\Test\Api
     */
    protected $api;
    
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Vnecoms\Core\Test\Api $api
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Vnecoms\Core\Test\Api $api, 
        array $data = []
    ) {
        $this->api = $api;
        parent::__construct($context, $data);
    }
    
    /**
     * Renders grid column
     *
     * @param   Object $row
     * @return  string
     */
    public function render(DataObject $row)
    {
        return $this->api->renderLicenseInfo($row);
    }
}