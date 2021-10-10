<?php
namespace Vnecoms\Sms\Block\Adminhtml\System\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Config;

class PaymentColumn extends \Magento\Framework\View\Element\Html\Select
{

    protected $excludes = [];

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var Config
     */
    protected $paymentModelConfig;
    
    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param Config $paymentModelConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        Config $paymentModelConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $context->getScopeConfig();
        $this->paymentModelConfig = $paymentModelConfig;
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
                'label' => __('Payment Method'),
                'value' => $this->getPaymentMethodOptionColumns()
            ]
        ];
    }

    /**
     * @return array
     */
    public function getPaymentMethodOptionColumns()
    {
        $payments = $this->paymentModelConfig->getActiveMethods();
        $methods = [];
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->scopeConfig
                ->getValue('payment/'.$paymentCode.'/title');
            $methods[$paymentCode] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode
            );
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
