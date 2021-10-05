<?php
namespace Applab\Brands\Controller\Adminhtml\Brand;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Helper\Js $jsHelper,
        \Applab\Brands\Helper\Data $helperData
        ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
    	return $this->_authorization->isAllowed('Ves_Brand::brand_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    { 
    	$data = $this->getRequest()->getPostValue(); // echo '<pre>'; print_r($data); echo $data['thumbnail']['value']; exit('hai');

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Ves\Brand\Model\Brand');

            $id = $this->getRequest()->getParam('brand_id');
            $isNew = true;
            $brand_image = $brand_thumbnail = "";
            if ($id) {
                $model->load($id);
                $brand_image = $model->getImage();
                $brand_thumbnail = $model->getThumbnail();
                $currentName = $model->getName();
                $currentNameAr = $model->getNameAr();
                $isNew = false;
            }
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'ves/brand/';
            $path = $mediaDirectory->getAbsolutePath($mediaFolder);

            // Delete, Upload Image
            $imagePath = $mediaDirectory->getAbsolutePath($model->getImage());
            if(isset($data['image']['delete']) && file_exists($imagePath)){
                unlink($imagePath);
                $data['image'] = '';
                if($brand_image && $brand_thumbnail && $brand_image == $brand_thumbnail){
                    $data['thumbnail'] = '';
                }
            }
            if(isset($data['image']) && is_array($data['image'])){
                unset($data['image']);
            }
            if($image = $this->uploadImage('image')){
                
                $data['image'] = $image;
            }

            // Delete, Upload Thumbnail
            $thumbnailPath = $mediaDirectory->getAbsolutePath($model->getThumbnail());
            if(isset($data['thumbnail']['delete']) && file_exists($thumbnailPath)){
                unlink($thumbnailPath);
                $data['thumbnail'] = '';
                if($brand_image && $brand_thumbnail && $brand_image == $brand_thumbnail){
                    $data['image'] = '';
                }
            }
            if(isset($data['thumbnail']) && is_array($data['thumbnail'])){
                unset($data['thumbnail']);
            }
            if($thumbnail = $this->uploadImage('thumbnail')){
                $data['thumbnail'] = $thumbnail;
            }

            if($data['url_key']=='')
            {
                $data['url_key'] = $data['name'];
            }
            $url_key = $this->_objectManager->create('Magento\Catalog\Model\Product\Url')->formatUrlKey($data['url_key']);
            $data['url_key'] = $url_key;

            $links = $this->getRequest()->getPost('links');
            $links = is_array($links) ? $links : [];
            if(!empty($links) && isset($links['related'])){
                $products = $this->jsHelper->decodeGridSerializedInput($links['related']);
                $data['products'] = $products;
            }

            /**** update product attribute brands***/
                $thumbnail = isset($data['thumbnail']) ? $data['thumbnail'] : '';
                if($isNew){
                    $optionId = $this->helperData->setBrands($data['name'], $data['name_ar'], $thumbnail, $data['stores']);
                    $data['brand_map_id'] = $optionId;
                } else { 
                    if($data['name'] != $currentName || $data['name_ar'] != $currentNameAr  || $thumbnail != ''){ 
                        $this->helperData->updateBrandsData($currentName, $data['name'], $currentNameAr, $data['name_ar'], $thumbnail);
                    }
                }
            /**********************************/
            //echo '<pre>'; print_r($data); exit('hai');
            $model->setData($data);
            try {
                $model->save();
                $this->_eventManager->dispatch(
                    'ves_brand_controller_brand_save',
                    [
                        'brand_id' => $model->getId(),
                        'is_new' => $isNew,
                        'brand' => $model
                    ]
                );

                $this->messageManager->addSuccess(__('You saved this brand.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['brand_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) { $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the brand.'));
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['brand_id' => $this->getRequest()->getParam('brand_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function uploadImage($fieldId = 'image')
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name']!='') 
        {
            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => $fieldId)
                );
            $path = $this->_fileSystem->getDirectoryRead(
                DirectoryList::MEDIA
                )->getAbsolutePath(
                'catalog/category/'
                );

                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
                $mediaFolder = 'ves/brand/';
                try {
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
                    $uploader->setAllowRenameFiles(true);
                    $uploader->setFilesDispersion(false);
                    $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder)
                        );
                    return $mediaFolder.$result['name'];
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/edit', ['brand_id' => $this->getRequest()->getParam('brand_id')]);
                }
            }
            return;
        }
    }