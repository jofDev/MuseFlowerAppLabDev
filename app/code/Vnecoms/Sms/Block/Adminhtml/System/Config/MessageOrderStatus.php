<?php
/**
 * Copyright © Vnecoms. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Vnecoms\Sms\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class with class map capability
 *
 * ...
 */
class MessageOrderStatus extends AbstractFieldArray
{
    protected $columnsStatusRenderer;

    protected $messageColumnsRenderer;

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('order_status', [
            'label' => __('Order Status'),
            'renderer' => $this->getStatusColumnsRenderer()
        ]);
        $this->addColumn('message',[
            'label' => __('Message'),
            'renderer' => $this->getMessageColumnsRenderer()
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Message');
    }

    /**
     * Provide hash of current value so it gets preselected
     *
     * @param \Magento\Framework\DataObject $row
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->getStatusColumnsRenderer()->calcOptionHash($row->getData('order_status'))]
            = 'selected="selected"';
        $row->setData('option_extra_attrs', $optionExtraAttr);
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getStatusColumnsRenderer()
    {
        if (null === $this->columnsStatusRenderer) {
            $element = $this->getElement();
            $uniqId = md5($element->getHtmlId() . $element->getScope() . $element->getScopeId());
            $this->columnsStatusRenderer = $this->getLayout()->createBlock(
                StatusColumns::class,
                'vnecoms_sms_system_config_status_columns_' . $uniqId,
                [
                    'data' => [
                        'is_render_to_js_template' => true
                    ]
                ]
            );
        }
        return $this->columnsStatusRenderer;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getMessageColumnsRenderer()
    {
        if (null === $this->messageColumnsRenderer) {
            $element = $this->getElement();
            $uniqId = md5($element->getHtmlId() . $element->getScope() . $element->getScopeId());
            $this->messageColumnsRenderer = $this->getLayout()->createBlock(
                MessagesColumns::class,
                'vnecoms_sms_system_config_message_columns_' . $uniqId,
                [
                    'data' => [
                        'is_render_to_js_template' => true,
                        'uiid' => $uniqId
                    ]
                ]
            );
        }
        return $this->messageColumnsRenderer;
    }

    public function getHtmlId()
    {
        return $this->getElement()->getHtmlId();
    }
}
