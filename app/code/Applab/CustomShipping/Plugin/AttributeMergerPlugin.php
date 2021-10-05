<?php
namespace Applab\CustomShipping\Plugin;

class AttributeMergerPlugin
{
    public function afterMerge(\Magento\Checkout\Block\Checkout\AttributeMerger $subject, $result)
    {

        // add Additional class for country
        if (array_key_exists('country_id', $result)) {
              $result['country_id']['additionalClasses'] = 'custom-class-country';
        }
        // add Additional class for city
        if (array_key_exists('city', $result)) {
            $result['city']['additionalClasses'] = 'cusotom-city';
              $result['city']['value'] = 'Qatar';
        }

        if (array_key_exists('telephone', $result)) {
            $result['telephone']['validation'] = [
                'required-entry'  => true,
                'max_text_length' => 8,
                'min_text_length' => 8,
                'validate-number' => true
            ];
        }
        return $result;
    }
}