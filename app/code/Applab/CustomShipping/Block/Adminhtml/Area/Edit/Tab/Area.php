<?php


namespace Applab\CustomShipping\Block\Adminhtml\Area\Edit\Tab;
/**
 * Description of Area
 *
 * @author joffy
 */
class Area extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    protected $countryFactory;

     protected $regionFactory;

    /**
     * 
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        array $data = array()) 
    {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->yesNo = $yesNo;
    }
    
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm() 
    {    	
    	$model = $this->_coreRegistry->registry('customshipping_area');        


        $regionCollection = $this->regionFactory->create()->getCollection()->addCountryFilter('QA');
        $regions = $regionCollection->toOptionArray();
    
    	$form = $this->_formFactory->create();

    	$fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Area Information')]);
    	$areaId = NULL;
        if ($model->getId()) {
            $fieldset->addField('area_id', 'hidden', ['name' => 'area_id']);
            $areaId = $model->getAreaId();
        }
        
        $country = $fieldset->addField(
            'area_zone_id',
            'select',
            [
                'name' => 'area_zone_id',
                'label' => __('Area Or Zone Number'),
                'title' => __('Area Or Zone Number'),
                'values' => $regions,  
                'required' => true           
            ]
        );

        $fieldset->addField(
            'area_zone_name',
            'hidden', 
            [
                'name' => 'area_zone_name',           
            ]
        );
  
 /*
         * Add Ajax to the Country select box html output
         */
        $country->setAfterElementHtml("<script type=\"text/javascript\">
                    require([
                    'jquery',
                    'mage/template',
                    'jquery/ui',
                    'mage/translate'
                ],
                function($, mageTemplate) {
                
                    $(document).ready(function(){


                 $('#edit_form').on('change', '#area_zone_id', function(event){
                     var area_zone_name = $('#area_zone_id option:selected').text();
                      $('#area_zone_name').val(area_zone_name);

                   })
                });
             });
            </script>");

        
        $fieldset->addField(
            'is_in_remorte_area',
            'select',
            [
                'label' => __('Is in OutSide Doha Area ?'),
                'title' => __('Is in OutSide Doha Area ?'),
                'name' => 'is_in_remorte_area',
                'type' => 'options',
                'values' => $this->yesNo->toOptionArray(),
                'required' => true
            ]
        );

      


        

    	$form->setValues($model->getData());
    	$this->setForm($form);

    	return parent::_prepareForm();
    }


    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Area Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Area Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}