<?php
/**
 * Copyright © Vnecoms. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Vnecoms\Sms\Block\Adminhtml\System\Config;

class StatusColumns extends \Magento\Framework\View\Element\Html\Select
{

    protected $excludes = [];

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\Collection
     */
    protected $orderStatusSource;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\Status\Collection $orderStatusSource,
        array $data = []
    ) {
        $this->orderStatusSource = $orderStatusSource;
        parent::__construct($context, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getColumns());
        }
        return parent::_toHtml();
    }

    /**
     * @return array
     */
    protected function getColumns()
    {
        return [
            [
                'label' => __('Order Status'),
                'value' => $this->getOrderStatusOptionColumns()
            ]
        ];
    }

    /**
     * @return array
     */
    public function getOrderStatusOptionColumns()
    {
        $allOptionColumns = $this->orderStatusSource->toOptionArray();
        return $allOptionColumns;
    }

    /**
     * @param string $value
     * @return \Vnecoms\Sms\Block\Adminhtml\System\Config\StatusColumns
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
