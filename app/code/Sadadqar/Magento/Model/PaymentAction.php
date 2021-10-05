<?php 

namespace Sadadqar\Magento\Model;

use \Magento\Framework\Option\ArrayInterface;

class PaymentAction implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => \Sadadqar\Magento\Model\PaymentMethod::ACTION_AUTHORIZE,
                'label' => __('Authorize')
            ]
        ];
    }
}