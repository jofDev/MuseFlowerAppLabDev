<?php
namespace Vnecoms\Core\Block\Adminhtml\Key;

/**
 * CMS block edit form container
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Vnecoms_Core';
        $this->_controller = 'adminhtml_key';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('delete', 'label', __('Delete License'));
        if($this->getLicense()->getId()){
            $this->addButton(
                'sync',
                [
                    'label' => __('Sync License'),
                    'onclick' => 'setLocation(\'' . $this->getSyncLicenseUrl() . '\')',
                    'class' => 'save'
                ]
            );
        }
    }

    /**
     * Get License
     */
    public function getLicense(){
        return $this->_coreRegistry->registry('current_license');
    }
    
    /**
     * Get Sync License URL
     */
    public function getSyncLicenseUrl(){
        return $this->getUrl('*/*/load',['id' => $this->getLicense()->getId()]);
    }
    
    /**
     * Get edit form container header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->getLicense()->getId()){
            return $this->getLicense()->getLicenseKey();
        }
		
		return __('New License');
    }
}
