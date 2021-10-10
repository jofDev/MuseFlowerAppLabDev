<?php 

namespace Applab\EventsPro\Ui\Component\Form;

use Magento\Framework\Registry;
use Magento\Framework\App\Filesystem\DirectoryList;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider {

  protected $loadedData;
  
  public function __construct(
    string $name,
    string $primaryFieldName,
    string $requestFieldName,
    Registry $registry,
    \Applab\EventsPro\Model\ResourceModel\EventsProData\CollectionFactory $eventsProDataCollectionFactory,
    array $meta = [],
    array $data = [],
    \Magento\Framework\Filesystem $filesystem
  )
  {
      parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
      $this->registry   = $registry;
      $this->collection = $eventsProDataCollectionFactory->create();
      $this->_filesystem = $filesystem;
  }

  public function getData()
  {

     if (isset($this->loadedData)) {
            return $this->loadedData;
      }
      $items = $this->collection->getItems();
      $mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
      foreach ($items as $item) {
            $this->loadedData[$item->getId()] = $item->getData();

            if($item->getEventBanner()) {
              $type = 'image/'.pathinfo(basename($item->getEventBanner()),PATHINFO_EXTENSION);
                      $m['event_banner'][0]['name']         = basename($item->getEventBanner());
                      $m['event_banner'][0]['url']          = $item->getEventBanner();
                      $m['event_banner'][0]['previewType']  = 'image';
                      $m['event_banner'][0]['type']         = $type;
                      $m['event_banner'][0]['size']         = filesize($mediapath.'eventspro/'.basename($item->getEventBanner()));
                      $fullData = $this->loadedData;
                      $this->loadedData[$item->getId()]     = array_merge($fullData[$item->getId()], $m);
            }

            if($item->getEventBannerAr()) {
              $typeAr = 'image/'.pathinfo(basename($item->getEventBannerAr()),PATHINFO_EXTENSION);
                      $m['event_banner_ar'][0]['name']      = basename($item->getEventBannerAr());
                      $m['event_banner_ar'][0]['url']       = $item->getEventBannerAr();
                      $m['event_banner_ar'][0]['previewType']  = 'image';
                      $m['event_banner_ar'][0]['type']      = $typeAr;
                      $m['event_banner_ar'][0]['size']      = filesize($mediapath.'eventspro/'.basename($item->getEventBannerAr()));
                      $fullData = $this->loadedData;
                      $this->loadedData[$item->getId()]     = array_merge($fullData[$item->getId()], $m);
            }
      }
      return $this->loadedData;
    
  }
}