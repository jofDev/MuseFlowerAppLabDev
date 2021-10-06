<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Core\Block\Adminhtml\Key\Edit\Renderer;

use Magento\Backend\Block\Widget;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * Adminhtml tier price item renderer
 */
class Domains extends Widget implements RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'key/edit/renderer/domains.phtml';
    
    /**
     * Prepare global layout
     * Add "Add tier" button to layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Add Domain'), 'onclick' => 'return domainsControl.addItem()', 'class' => 'add']
        );
        $button->setName('add_domain_item_button');
    
        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }
    
    /**
     * Render HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
    
    /**
     * Set form element instance
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Group\AbstractGroup
     */
    public function setElement(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this;
    }
    
    /**
     * Retrieve form element instance
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }
    
    /**
     * Prepare group price values
     *
     * @return array
     */
    public function getValues()
    {
        $values = [];
        $data = $this->getElement()->getValue();
        foreach($data as $domain){
            $values[] = ['domain' => $domain];
        }
        return $values;
    }
    
    /**
     * Retrieve 'add group price item' button HTML
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    
}
