<?php

namespace Vnecoms\Sms\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class with class map capability
 *
 * ...
 */
class NewShipmentByShipping extends AbstractFieldArray
{
    protected $columnPaymentRenderer;

    protected $messageColumnsRenderer;

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn('shipping_method', [
            'label' => __('Shipping Method'),
            'renderer' => $this->getPaymentMethodColumnsRenderer()
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
        $optionExtraAttr['option_' . $this->getPaymentMethodColumnsRenderer()->calcOptionHash($row->getData('shipping_method'))]
            = 'selected="selected"';
        $row->setData('option_extra_attrs', $optionExtraAttr);
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getPaymentMethodColumnsRenderer()
    {
        if (null === $this->columnPaymentRenderer) {
            $element = $this->getElement();
            $uniqId = md5($element->getHtmlId() . $element->getScope() . $element->getScopeId());
            $this->columnPaymentRenderer = $this->getLayout()->createBlock(
                ShipmentColumn::class,
                'vnecoms_sms_system_config_new_shipment_shippingmethod_columns_' . $uniqId,
                [
                    'data' => [
                        'is_render_to_js_template' => true
                    ]
                ]
            );
        }
        return $this->columnPaymentRenderer;
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
                'vnecoms_sms_system_config_new_shipment_message_columns_' . $uniqId,
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
