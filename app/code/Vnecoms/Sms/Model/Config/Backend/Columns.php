<?php
/**
 * Copyright © Vnecoms. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vnecoms\Sms\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class Columns extends ArraySerialized
{
    public function beforeSave()
    {
        $values = $this->getValue();
        /* Check duplicate status order */
        if ($values) {
            if (!is_array($values)) {
                $values = json_decode($values, true);
            }
            $check = [];
            $newValue = [];
            foreach ($values as $key=>$value) {
                if (!isset($value['order_status'])) continue;
                if (isset($check[$value['order_status']])) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Each status can only appear once.')
                    );
                }
                $newValue[$key] = $value;
                $check[$value['order_status']] = true;
            }
        }
        if (is_array($newValue)) {
            unset($newValue['__empty']);
            $this->setValue(json_encode($newValue));
        }
        parent::beforeSave();
    }
		
		/**
     * @return void
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        if (!is_array($value)) {
            try{
                $value = empty($value) ? false : json_decode($value, true);
    		}catch(\Exception $e){
    			$value = false;
    		}
        }
        if(!$value) $value = [];
        $this->setValue($value);
    }
}
