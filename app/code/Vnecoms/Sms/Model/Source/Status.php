<?php
namespace Vnecoms\Sms\Model\Source;

use Vnecoms\Sms\Model\Sms;

class Status extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{


    /**
     * Options array
     *
     * @var array
     */
    protected $_options = null;
    
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Failed'), 'value' => Sms::STATUS_FAILED],
                ['label' => __('Pending'), 'value' => Sms::STATUS_PENDING],
                ['label' => __('Sent'), 'value' => Sms::STATUS_SENT],
                ['label' => __('Delivered'), 'value' => Sms::STATUS_DELIVERED],
                ['label' => __('Undelivered'), 'value' => Sms::STATUS_UNDELIVERED],
                ['label' => __('Failed (not enough credit)'), 'value' => Sms::STATUS_NOT_ENOUGH_CREDIT],
            ];
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
    
    
    /**
     * Get options as array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
