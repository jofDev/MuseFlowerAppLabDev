<?php
namespace Vnecoms\Sms\Model\Config\Source;

class CodeFormat extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    const TYPE_ALPHANUM = 'alphanum';
    const TYPE_ALPHA    = 'alpha';
    const TYPE_NUM      = 'num';
    
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
                ['label' => __('Alphanumeric'), 'value' => self::TYPE_ALPHANUM],
                ['label' => __('Alphabetical'), 'value' => self::TYPE_ALPHA],
                ['label' => __('Numeric'), 'value' => self::TYPE_NUM],
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
