<?php

namespace Vnecoms\Sms\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class NewOrderByPayment extends ArraySerialized
{
    public function beforeSave()
    {
        $values = $this->getValue();
        /* Check duplicate status order */
        if ($values) {
            if (!is_array($values)) {
                $values = json_decode($values, true);
            }
            $newValue = [];
            foreach ($values as $key=>$value) {
                if (!isset($value['payment_method'])) continue;
                $method = $value['payment_method'];
                if (isset($newValue[$method])) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Message for payment method %1 is duplicated.', $method)
                    );
                }
                $newValue[$method] = $value;
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
                $value = json_decode($value, true);
            }catch(\Exception $e){
                $value = false;
            }
        }
        if(!is_array($value)) $value = [];
        $this->setValue($value);
    }
}
