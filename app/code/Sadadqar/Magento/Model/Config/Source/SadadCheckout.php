<?php

namespace  Sadadqar\Magento\Model\Config\Source;

use \Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Profile
 * @package Vendor\Package\Model\Config\Source
 */
class SadadCheckout implements OptionSourceInterface {

    /*public function __construct(
    \Magento\Backend\Block\Template\Context $context, array $data = []
    ) {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
        $value = $element->getData('value');

        //YOUR IF CONDITION FOR CHECKING DROPDOWN OPTIONS

        $options = array( 1 =>'Web Checkout', 2 => 'Web Checkout 2.2'); // Your Select Options
        $name = "groups[sadadqa][fields][checkout_type][value]"; // Make sure you are using correct path for your field
        $id = "checkout_type";

        $html = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
            )->setName(
                $name
            )->setId(
                $id
            )->setTitle(
                __("")
            )->setValue(
                $value
            )->setOptions(
                $options
            )->setExtraParams(
                'data-validate="{\'validate-select\':true}"'
            )->getHtml();

        // ELSE

        $html = '<p>There is no option available.</p>';
        $html .= '<script type="text/javascript">
           require(["jquery"], function ($) {
                $(document).ready(function () {
                    $(".hide-this").each(function(){
                        $(this).closest("tr").hide(); // Can also use .remove() to remove the field completely
                    })
                });
            });
        </script>';

        // END IF
        return $html;
    }*/
   public function toOptionArray() : array
    {
        return [
            ['value' => '1', 'label' => 'Web Checkout'],
            ['value' => '2', 'label' => 'Web Chekcout 2.2']
        ];
    }
}