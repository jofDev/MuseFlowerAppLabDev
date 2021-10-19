<?php
namespace Applab\CustomShipping\Plugin\Checkout\Model\Checkout;

class LayoutProcessor
{
    /**
    * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
    * @param array $jsLayout
    * @return array
    */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) 
    {
       

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['value'] = 'Qatar';

        return $jsLayout;
    }
}