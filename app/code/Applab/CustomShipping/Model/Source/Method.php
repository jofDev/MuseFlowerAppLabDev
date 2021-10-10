<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile
namespace Applab\CustomShipping\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
/**
* Method source
*/
class Method implements OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $types = [];
        
        $types[] = ['value' => 'regular_shipping', 'label' => __('Regular Shipping')];
     //   $types[] = ['value' => 'express_shipping', 'label' => __('One Day Shipping')];
        
        return $types;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $types = [];        
        $types['regular_shipping'] = __('Regular Shipping');
      //  $types['express_shipping'] = __('One Day Shipping');
        return $types;
    }
}
