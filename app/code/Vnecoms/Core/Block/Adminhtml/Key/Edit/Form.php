<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Core\Block\Adminhtml\Key\Edit;

/**
 * Adminhtml cms block edit form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_statuses = [
        0 => "Expired",
        1 => "Pending",
        2 => "Available",
        3 => "Suspended",
    ];
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('license_form');
        $this->setTitle(__('License Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_license');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $model->getId()?$this->getUrl('*/*/save'):$this->getUrl('*/*/load'),
                    'method' => 'post'
                ]
                
            ]
        );

        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('License Information'), 'class' => 'fieldset-wide license-fieldset']
        );
        
		/*Add new license*/
		$fieldset->addField(
			'license_key',
			$model->getId()?'label':'text',
			[
				'name' => 'license_key',
				'label' => __('License Key'), 
				'title' => __('License Key'), 
				'required' => true, 
				'note' => $model->getId()?'':__("Enter the license key which you got when you purchase an extension from www.vnecoms.com.")
			]
		);

        if ($model->getId()) {
            $data = array_merge($model->getSavedKeyInfo(), $model->getData());
            $data['licensed_extensions'] = is_array($data['licensed_extensions'])?implode("<br />", $data['licensed_extensions']):'N/A';
            $data['license_key'] = $model->getLicenseKey();
            $data['status'] = isset($data['status'])?isset($this->_statuses[$data['status']])?$this->_statuses[$data['status']]:'N/A':'N/A';
            $data['expiry_at'] = isset($data['expiry_at'])?$data['expiry_at']:'N/A';
            /*Edit the license*/
            $fieldset->addField('key_id', 'hidden', ['name' => 'key_id']);

            $fieldset->addField(
                'item_name',
                'label',
                ['name' => 'item_name', 'label' => __('Extension'), 'title' => __('Extension')]
            );
            $fieldset->addField(
                'type',
                'label',
                ['name' => 'type', 'label' => __(' License Type'), 'title' => __(' License Type')]
            );
            $fieldset->addField(
                'created_at',
                'label',
                ['name' => 'created_at', 'label' => __('Created At'), 'title' => __('Created At')]
            );
            $fieldset->addField(
                'expiry_at',
                'label',
                ['name' => 'expiry_at', 'label' => __('Expiration date'), 'title' => __('Expiration date')]
            );
            $fieldset->addField(
                'status',
                'label',
                ['name' => 'status', 'label' => __('Status'), 'title' => __('Status')]
            );
            $notes = [
                __("Your current secure key is: %1",sprintf('<strong style="font-weight: bold; color: #eb5202">%s</strong>',$model->getSecureKey())),
            ];
            if(!$data['secure_key']){
                $notes[] = '<span class="admin__field-error" style="margin-top: 10px;">'.__('This is the first time you use the license key. The secure key will be updated to the license after you click to save button. Let add your domains and click to save button to active the extension.').'</span>';
            }elseif($model->getSecureKey() != $data['secure_key']){
                $notes[] = '<span class="admin__field-error" style="margin-top: 10px;">'.__('Your secure key is different with the registered secure key. The related extension(s) will not be acitvated.<br />Let login to your customer email account at VNECOMS.COM, edit your license key and update your new secure key.').'</span>';
            }
            $fieldset->addField(
                'secure_key',
                'label',
                [
                    'name' => 'secure_key', 
                    'label' => __('Secure Key'), 
                    'title' => __('Secure Key'),
                    'note' => implode("<br />",$notes),
                ]
            );
            $fieldset->addField(
                'licensed_extensions',
                'note',
                ['name' => 'licensed_extensions', 'label' => __('Related Packages'), 'title' => __('Related Packages'), 'text' => $data['licensed_extensions']]
            );
            $fieldset->addField(
                'domains',
                'note',
                [
                    'name' => 'domains',
                    'label' => __('Domains'),
                    'title' => __('Domains'),
                ]
            )->setRenderer($this->getLayout()->createBlock('Vnecoms\Core\Block\Adminhtml\Key\Edit\Renderer\Domains'));
            $form->setValues($data);
        }
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
