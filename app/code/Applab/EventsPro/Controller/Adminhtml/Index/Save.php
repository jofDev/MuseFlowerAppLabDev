<?php

namespace Applab\EventsPro\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Validation\ValidationException;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Save extends \Magento\Backend\App\Action {

  protected $uploaderFactory;
  protected $eventsProDataFactory;
  protected $mediaDirectory;

  public function __construct(
    Context $context,
    UploaderFactory $uploaderFactory,
    Filesystem $filesystem,
    \Applab\EventsPro\Model\EventsProDataFactory $eventsProDataFactory,
    \Magento\Store\Model\StoreManagerInterface $storeManager
  )
  {
    parent::__construct($context);
    $this->uploaderFactory = $uploaderFactory;
    $this->eventsProDataFactory = $eventsProDataFactory;
    $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    $this->_storeManager = $storeManager;
  }

  public function execute()
  {
    try {

      if ($this->getRequest()->getMethod() !== 'POST' || !$this->_formKeyValidator->validate($this->getRequest())) {
        throw new LocalizedException(__('Invalid Request'));
      }
    $data     = $this->getRequest()->getPostValue(); //echo "<pre>"; print_r($data); exit();
    $mediaUrl = $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ).'eventspro/';
    //validate image
    $fileUploader = $fileUploaderAr = null;
    $params = $this->getRequest()->getParams();
    try {
          $banner = $banner_ar = '';
        //$imageId = 'event_banner';
        if (isset($params['event_banner']) && count($params['event_banner']) && array_key_exists("tmp_name",$params['event_banner'][0])) {
            $imageId = $params['event_banner'][0];
            if (!file_exists($imageId['tmp_name'])) {
                $imageId['tmp_name'] = $imageId['path'] . '/' . $imageId['file'];
            }

            $fileUploader = $this->uploaderFactory->create(['fileId' => $imageId]);
            $fileUploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
            $fileUploader->setAllowRenameFiles(true);
            $fileUploader->setAllowCreateFolders(true);
            $fileUploader->validateFile();
            $imageIdInfo = $fileUploader->save($this->mediaDirectory->getAbsolutePath('eventspro'));
            $banner      = $imageIdInfo['file'] ? $mediaUrl.$imageIdInfo['file'] : '';
        }
        

        //$imageIdAr = 'event_banner_ar';
        if (isset($params['event_banner_ar']) && count($params['event_banner_ar']) && array_key_exists("tmp_name",$params['event_banner_ar'][0])) {
            $imageIdAr = $params['event_banner_ar'][0];
            if (!file_exists($imageIdAr['tmp_name'])) {
                $imageIdAr['tmp_name'] = $imageIdAr['path'] . '/' . $imageIdAr['file'];
            }

            $fileUploaderAr = $this->uploaderFactory->create(['fileId' => $imageIdAr]);
            $fileUploaderAr->setAllowedExtensions(['jpg', 'jpeg', 'png']);
            $fileUploaderAr->setAllowRenameFiles(true);
            $fileUploaderAr->setAllowCreateFolders(true);
            $fileUploaderAr->validateFile();
            $imageIdInfoAr = $fileUploaderAr->save($this->mediaDirectory->getAbsolutePath('eventspro')); 
            $banner_ar     = $imageIdInfoAr['file'] ? $mediaUrl.$imageIdInfoAr['file'] : '';
        }
        

        $event_id = $this->getRequest()->getParam('id');
        if ($event_id) {
            $eventsProDataUpdate = $this->eventsProDataFactory->create()->load($event_id, 'id');
            
            $banner    = $banner ? $banner : $eventsProDataUpdate->getEventBanner();
            $banner_ar = $banner_ar ? $banner_ar : $eventsProDataUpdate->getEventBannerAr();
            try {
                $eventsProDataUpdate->setEventBanner($banner);
                $eventsProDataUpdate->setEventBannerAr($banner_ar);
                $eventsProDataUpdate->setEventName($params['event_name']);
                $eventsProDataUpdate->setEventNameAr($params['event_name_ar']);
                $eventsProDataUpdate->setEventDesc($params['event_desc']);
                $eventsProDataUpdate->setEventDescAr($params['event_desc_ar']);
                $eventsProDataUpdate->setStartDate($params['start_date']);
                $eventsProDataUpdate->setEndDate($params['end_date']);
                $eventsProDataUpdate->setStatus($params['status']);
                $eventsProDataUpdate->save();
                $this->messageManager->addSuccess(__('The data has been saved.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the data.'));
            }

            return $this->_redirect('*/*/edit', ['id' => $event_id]);
        } else {
 
            $eventsProData = $this->eventsProDataFactory->create();
            $eventsProData->setEventBanner($banner);
            $eventsProData->setEventBannerAr($banner_ar);
            $eventsProData->setEventName($params['event_name']);
            $eventsProData->setEventNameAr($params['event_name_ar']);
            $eventsProData->setEventDesc($params['event_desc']);
            $eventsProData->setEventDescAr($params['event_desc_ar']);
            $eventsProData->setStartDate($params['start_date']);
            $eventsProData->setEndDate($params['end_date']);
            $eventsProData->setStatus($params['status']);
            $eventsProData->setCreatedAt(date('y-m-d H:m:s'));
            $eventsProData->save();
        }

    } catch (ValidationException $e) {
      throw new LocalizedException(__('Image extension is not supported. Only extensions allowed are jpg, jpeg and png'));
    } catch (\Exception $e) { 
        //if an except is thrown, no image has been uploaded
        throw new LocalizedException(__('Error : '.$e->getMessage()));
    }
    $this->messageManager->addSuccessMessage(__('Event created successfully'));
    return $this->_redirect('*/*/index');
    } catch (LocalizedException $e) {
        $this->messageManager->addErrorMessage($e->getMessage());
        return $this->_redirect('*/*/index');
    } catch (\Exception $e) {
        error_log($e->getMessage());
        error_log($e->getTraceAsString());
        $this->messageManager->addErrorMessage(__('An error occurred, please try again later.'));
        return $this->_redirect('*/*/index');
    }

  }
}