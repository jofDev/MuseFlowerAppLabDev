<?php

namespace  Sadadqar\Magento\Model\Config\Source;

use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Profile
 * @package Vendor\Package\Model\Config\Source
 */
class SadadLang implements OptionSourceInterface
{
    /**
     * @return array
     */
	 //
   public function toOptionArray() : array
    {
        return [
            ['value' => 'arb', 'label' => 'Arabic'],
            ['value' => 'eng', 'label' => 'English']
        ];
    }
}