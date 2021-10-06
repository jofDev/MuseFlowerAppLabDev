<?php
namespace Vnecoms\Sms\Model\Config\Source;

use Magento\Framework\App\ObjectManager;

class Gateways extends \Magento\Framework\DataObject implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($data);
    }
    
    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value'=>'', 'label'=>'Disable']
        ];
        foreach($this->helper->getGateways() as $key => $gateway){
            if(!class_exists($gateway)){throw new \Exception(__("Class %1 does not exist", $gateway));}
            $gatewayModel = ObjectManager::getInstance()->create($gateway);
            $options[] = [
                'value' => $key,
                'label' => $gatewayModel->getTitle(),
            ];
        }
        return $options;
    }
}
