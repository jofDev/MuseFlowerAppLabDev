<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Applab\CustomShipping\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
/**
 * Description of CityActions
 *
 * @author dharmendra
 */
class AreaActions extends Column
{

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /** Url Path */
    const CITY_URL_PATH_EDIT = 'customshipping/area/edit';
    const CITY_URL_PATH_DELETE = 'customshipping/area/delete';
    
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = array(),
        UrlInterface $urlBuilder,
        array $data = array()) 
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }
    
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return void
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['area_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(self::CITY_URL_PATH_EDIT, ['area_id' => $item['area_id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::CITY_URL_PATH_DELETE, ['area_id' => $item['area_id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'message' => __('Are you sure you wan\'t to delete this record?')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}