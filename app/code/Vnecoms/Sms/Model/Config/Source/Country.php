<?php
namespace Vnecoms\Sms\Model\Config\Source;

class Country extends \Magento\Directory\Model\Config\Source\Country
{
    public function toOptionArray($isMultiselect = false, $foregroundCountries = ''){
        if (!$this->_options) {
            $this->_options = $this->_countryCollection->loadData()->setForegroundCountries(
                $foregroundCountries
            )->toOptionArray(
                false
            );
        }
        
        $options = $this->_options;
        array_unshift($options, ['value' => 'auto', 'label' => __('--Auto detected by customer IP--')]);
        return $options;
    }
}
