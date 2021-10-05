<?php
namespace Vnecoms\Sms\Block\Adminhtml\System\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Config\Source\Allmethods;

class ShipmentColumn extends \Magento\Framework\View\Element\Html\Select
{

    protected $excludes = [];

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Shipping\Model\Config\Source\Allmethods
     */
    protected $shippingMethods;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param Config $paymentModelConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        Allmethods $shippingMethods,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->shippingMethods = $shippingMethods;
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
                'label' => __('Shipping Method'),
                'value' => $this->getShipmentMethodOptionColumns()
            ]
        ];
    }

    /**
     * @return array
     */
    public function getShipmentMethodOptionColumns()
    {
        $shippingMethods = $this->shippingMethods->toOptionArray();
        $methods = [];
        foreach($shippingMethods as $shippingCode => $shipping){
            if(!$shipping['value']) continue;
            $methods[$shippingCode] = [
                'label' => $shipping['label'],
                'value' => $shippingCode
            ];
        }

        return $methods;
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
