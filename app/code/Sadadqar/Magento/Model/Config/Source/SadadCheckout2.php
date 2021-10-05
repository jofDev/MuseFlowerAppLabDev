<?php

namespace  Sadadqar\Magento\Model\Config\Source;

use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Profile
 * @package Vendor\Package\Model\Config\Source
 */
class SadadCheckout2 implements OptionSourceInterface
{
    /**
     * @return array
     */
	 //
   public function toOptionArray() : array
    {
        return [
            ['value' => '2', 'label' => 'Iframe'],
            ['value' => '1', 'label' => 'Modal Popup']
        ];
    }
}