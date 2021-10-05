<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Applab\CustomShipping\Controller\Adminhtml\Area;

/**
 * Description of RegionList
 *
 * @author dharmendra
 */
class RegionList extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Applab\SnoonuShipping\Model\Area
     */
    private $area;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Applab\CustomShipping\Model\Area $area,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->_countryFactory = $countryFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
        $this->area = $area;
    }
    
    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $countrycode = $this->getRequest()->getParam('country');
        $stateList = '';

        if ($countrycode != '') {
            $statearray =$this->_countryFactory->create()->setId(
                $countrycode
            )->getLoadedRegionCollection()->toOptionArray();

            $regionId = '';
            if($this->getRegionId()) {
                $regionId = $this->getRegionId();
            }
            
            if(!empty($statearray)) {
                foreach ($statearray as $state) {                
                    $selected = '';
                    if(!empty($regionId) && $state['value'] == $regionId) {
                        $selected = ' selected';
                    }
                    $stateList .= "<option value='".$state['value']."' $selected>" . $state['label'] . "</option>";
                }
            }
        }

        if(!empty($stateList)) {
            $result['htmlconent'] = $stateList;
        } else {
            $result['htmlconent'] = '';
        }

        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result)
        );
    }
    
    private function getRegionId() {
        $areaId = $this->getRequest()->getParam('area_id', false);
        if($areaId) {
            $area = $this->area->load($areaId);
            if($area->getAreaId()) {
                return $area->getRegionId();
            }
        }
        
        return false;
    }

}