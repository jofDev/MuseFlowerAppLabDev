<?php
/**
 * Copyright © Vnecoms. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Vnecoms\Sms\Block\Adminhtml\System\Config;

/**
 * Class with class map capability
 *
 * ...
 */
class MessagesColumns extends \Magento\Framework\View\Element\Template
{
    protected $_renderer;

    protected $_type;

    /**
     * Render HTML
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _toHtml()
    {
        $html = '<textarea class="textarea admin__control-textarea" id="' .$this->getInputId(). '" rows="2" cols="15" name="' .$this->getName(). '" '
        . $this->serialize($this->getHtmlAttributes()) . ' >';
        $html .= ($this->getValue() ?: '');
        $html .= "</textarea>";
        return $html;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Return the attributes for Html.
     *
     * @return string[]
     */
    public function getHtmlAttributes()
    {
        return [
            'title',
            'class',
            'style',
            'onclick',
            'onchange',
            'rows',
            'cols',
            'readonly',
            'disabled',
            'onkeyup',
            'tabindex',
            'data-form-part',
            'data-role',
            'data-action'
        ];
    }

    /**
     * @param string $value
     * @return \Vnecoms\Sms\Block\Adminhtml\System\Config\MessagesColumns
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}