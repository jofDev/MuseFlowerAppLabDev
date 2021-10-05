<?php

namespace Vnecoms\Sms\Block\Adminhtml\System\Config;

/**
 * Fieldset renderer which expanded by default
 */
class Fieldset extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * Whether is collapsed by default
     *
     * @var bool
     */
    protected $isCollapsedDefault = false;
    
    /**
     * Return header title part of html for fieldset
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        return '<h4 id="' .
            $element->getHtmlId() .
            '-head" href="#' .
            $element->getHtmlId() .
            '-link">'.$element->getLegend().'</h4>';
        
        return '<a id="' .
            $element->getHtmlId() .
            '-head" href="#' .
            $element->getHtmlId() .
            '-link" onclick="Fieldset.toggleCollapse(\'' .
            $element->getHtmlId() .
            '\', \'' .
            $this->getUrl(
                '*/*/state'
            ) . '\'); return false;">' . $element->getLegend() . '</a>';
    }
    
    /**
     * Return header html for fieldset
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getHeaderHtml($element)
    {
        if ($element->getIsNested()) {
            $html = '<tr class="nested"><td colspan="4"><div class="' . $this->_getFrontendClass($element) . '">';
        } else {
            $html = '<div class="' . $this->_getFrontendClass($element) . '">';
        }

        $html .= '<div class="entry-edit-head admin__collapsible-block">' .
            '<span id="' .
            $element->getHtmlId() .
            '-link" class="entry-edit-head-link"></span>';

        $html .= $this->_getHeaderTitleHtml($element);

        $html .= '</div>';
        $html .= '<fieldset class="' . $this->_getFieldsetCss() . '" id="' . $element->getHtmlId() . '">';
        $html .= '<legend>' . $element->getLegend() . '</legend>';

        $html .= $this->_getHeaderCommentHtml($element);

        // field label column
        $html .= '<table cellspacing="0" class="form-list"><colgroup class="label" /><colgroup class="value" />';
        if ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) {
            $html .= '<colgroup class="use-default" />';
        }
        $html .= '<colgroup class="scope-label" /><colgroup class="" /><tbody>';

        return $html;
    }
    
    /**
     * Return full css class name for form fieldset
     *
     * @return string
     */
    protected function _getFieldsetCss()
    {
        /** @var \Magento\Config\Model\Config\Structure\Element\Group $group */
        $group = $this->getGroup();
        $configCss = $group->getFieldsetCss();
        return 'admin__collapsible-block' . ($configCss ? ' ' . $configCss : '');
    }
    
    /**
     * Return js code for fieldset:
     * - observe fieldset rows;
     * - apply collapse;
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getExtraJs($element)
    {
        return '';
    }
}
