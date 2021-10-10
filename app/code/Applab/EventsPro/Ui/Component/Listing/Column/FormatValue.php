<?php
namespace Applab\EventsPro\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class FormatValue extends \Magento\Ui\Component\Listing\Columns\Column
{   
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = [])
    {
     
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    { 
        $fieldName  = $this->getData('name');

        if (isset($dataSource['data']['items'])) { 
            foreach ($dataSource['data']['items'] as & $item) {

                if($fieldName == 'status'){
                    $eventId = $item['id'];  
                    if($eventId){
                        $status =  $item['status'] == 1 ? 'Enabled' : 'Disabled';
                        $item[$fieldName] = $status;
                    }
                } 

                if($fieldName == 'event_banner_ar'){
                    $eventId = $item['id'];  
                    if($eventId){
                        $item[$fieldName . '_src'] = $item['event_banner_ar'];
                    }
                }

                if($fieldName == 'event_banner'){
                    $eventId = $item['id'];  
                    if($eventId){
                         $item[$fieldName . '_src'] = $item['event_banner'];
                    }
                }
                
            }
        }
        return $dataSource;
    }
}