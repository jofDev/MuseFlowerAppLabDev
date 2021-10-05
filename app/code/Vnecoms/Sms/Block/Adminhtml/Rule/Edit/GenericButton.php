<?php

namespace Vnecoms\Sms\Block\Adminhtml\Rule\Edit;

use Magento\Backend\Block\Widget\Context;

/**
 * Class GenericButton
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected $context;
    
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }
    
    /**
     * @return int|null
     */
    public function getRuleId()
    {
        return $this->context->getRequest()->getParam('id');
    }
    
    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
