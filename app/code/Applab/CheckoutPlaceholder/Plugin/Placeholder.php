<?php
namespace Applab\CheckoutPlaceholder\Plugin;

class Placeholder
{
  public function afterMerge(\Magento\Checkout\Block\Checkout\AttributeMerger $subject, $result)
  {
    if (array_key_exists('street', $result)) {
      $result['street']['children'][0]['placeholder'] = __('Building Number/Address Line 1');
      $result['street']['children'][1]['placeholder'] = __('Street Number/Address Line 2');
      $result['street']['children'][2]['placeholder'] = __('Zone Number');
      $result['street']['children'][3]['placeholder'] = __('Appartment/Office No.');
    }

    return $result;
  }
}