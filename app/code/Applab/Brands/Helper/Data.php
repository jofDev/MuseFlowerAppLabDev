<?php

namespace Applab\Brands\Helper;

use Magento\Swatches\Helper\Data as SwatchData;
use Magento\Framework\Serialize\SerializerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $productFactory;
    protected $filesystem;
    protected $swatchHelper;
    protected $productMediaConfig;
    protected $driverFile;
    protected $attributeRepository;
    protected $productCollectionFactory;
    protected $productRepository;

    private $serializer;

	public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Swatches\Helper\Media $swatchHelper,
        \Magento\Catalog\Model\Product\Media\Config $productMediaConfig,
        \Magento\Framework\Filesystem\Driver\File $driverFile,
        \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetupInterface,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        SwatchData $swatchData

	) {
		parent::__construct($context);    
        $this->attributeRepository = $attributeRepository;
        $this->filesystem = $filesystem;
        $this->swatchHelper = $swatchHelper;
        $this->productMediaConfig = $productMediaConfig;
        $this->driverFile = $driverFile;
        $this->_productFactory = $productFactory;   
        $this->attributeFactory = $attributeFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetupInterface = $moduleDataSetupInterface;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productRepository = $productRepository;
        $this->swatchData = $swatchData;
	}	

	public function setBrands($brand_name, $brand_name_ar, $image = '', $stores){ //echo $brand_name.$image.$stores[0]; exit('-----');

        $isAttributeExist = $this->_productFactory->create()->getResource()->getAttribute('brands');

        if ($isAttributeExist && $isAttributeExist->usesSource()) {

            // check option exits by name
            $getOptionId = $this->checkOptionExits($brand_name);
            if($getOptionId){
                return false;
            }

            $attributesOptionsData = [
                'brands' => [
                    \Magento\Swatches\Model\Swatch::SWATCH_INPUT_TYPE_KEY => \Magento\Swatches\Model\Swatch::SWATCH_INPUT_TYPE_VISUAL,
                    'optionvisual' => [
                        'value'     => [
                            'option_0' => [
                                0 => $brand_name, //admin view
                                1 => $brand_name_ar, // arabic
                                2 => $brand_name, // english
                            ],
                        ],
                    ],
                    'swatchvisual' => [
                        'value'     => [
                            'option_0' => $image
                        ],
                    ],
                ]
            ];

            // Add order if it doesn't exist. This is an important step to make sure everything will be created correctly.
            foreach ($attributesOptionsData as &$attributeOptionsData) {
                $order = 0;
                $swatchVisualFiles = isset($attributeOptionsData['optionvisual']['value']) ? $attributeOptionsData['optionvisual']['value'] : [];
                foreach ($swatchVisualFiles as $index => $swatchVisualFile) {
                    if (!isset($attributeOptionsData['optionvisual']['order'][$index])) {
                        $attributeOptionsData['optionvisual']['order'][$index] = ++$order;
                    }
                }
            }

            // Prepare visual swatches files.
            $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $tmpMediaPath = $this->productMediaConfig->getBaseTmpMediaPath();
            $fullTmpMediaPath = $mediaDirectory->getAbsolutePath($tmpMediaPath);
            $this->driverFile->createDirectory($fullTmpMediaPath);
            foreach ($attributesOptionsData as &$attributeOptionsData) {
                $swatchVisualFiles = $attributeOptionsData['swatchvisual']['value'] ?? []; 
                foreach ($swatchVisualFiles as $index => $swatchVisualFile) {
                    if($swatchVisualFile){
                        $swatchVisualFileTmp = str_replace('ves/brand/','',$swatchVisualFile);
                        $this->driverFile->copy(
                            $mediaDirectory->getAbsolutePath($swatchVisualFile),
                            $fullTmpMediaPath . DIRECTORY_SEPARATOR .$swatchVisualFileTmp
                        );
                        $newFile = $this->swatchHelper->moveImageFromTmp($swatchVisualFileTmp);
                        if (substr($newFile, 0, 1) == '.') {
                            $newFile = substr($newFile, 1); // Fix generating swatch variations for files beginning with ".".
                        }
                        $this->swatchHelper->generateSwatchVariations($newFile);
                        $attributeOptionsData['swatchvisual']['value'][$index] = $newFile;
                    }
                }
            }
            try{
                // Add attribute options.
                foreach ($attributesOptionsData as $code => $attributeOptionsData) {
                    /* @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
                    $attribute = $this->attributeRepository->get($code);
                    $attribute->addData($attributeOptionsData);
                    $attribute->save();
                }
                // return option id to save in seller table
                return $this->checkOptionExits($brand_name);
            }catch(\Exception $e){
                return false;
            }
        } else { 
            return false;
        }
    }

    public function checkOptionExits($label) {
        $option_id = '';
        $attribute = $this->attributeRepository->get('brands')->setStoreId(0);
        if ($attribute && $attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions();
            
            foreach ($options as $option) {
                if(trim($option['label']) == trim($label)){
                    $option_id = $option['value'];
                    break;
                }
            }
        }
        return $option_id;
    }

    public function updateBrandsData($label, $newlabel, $label_ar, $newlabel_ar, $image)
    { 
        $option_id = $this->checkOptionExits($label);

        if($option_id && $image){

            /****************** update image ********************************/
            $mediaDirectory     = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $tmpMediaPath       = $this->productMediaConfig->getBaseTmpMediaPath();
            $fullTmpMediaPath   = $mediaDirectory->getAbsolutePath($tmpMediaPath);
            $this->driverFile->createDirectory($fullTmpMediaPath);
                        $swatchVisualFileTmp = str_replace('ves/brand/','',$image);
                        $this->driverFile->copy(
                            $mediaDirectory->getAbsolutePath($image),
                            $fullTmpMediaPath . DIRECTORY_SEPARATOR .$swatchVisualFileTmp
                        );
                        $newFile = $this->swatchHelper->moveImageFromTmp($swatchVisualFileTmp);
                        if (substr($newFile, 0, 1) == '.') {
                            $newFile = substr($newFile, 1); // Fix generating swatch variations for files beginning with ".".
                        }
            $this->swatchHelper->generateSwatchVariations($newFile);

            /********************************************************/
            $attribute = $this->attributeRepository->get('brands');
            if ($attribute && $attribute->usesSource()) {
                    $attribute->setData('swatchvisual', array('value' => array( $option_id => $newFile),   ));
                $attribute->save();
            }
        }
        if($option_id && $label != $newlabel || $label_ar != $newlabel_ar){ 

            $englishLabel = ($label != $newlabel) ? $newlabel : $label;
            $arabicLabel  = ($label_ar != $newlabel_ar) ? $newlabel_ar : $label_ar;

            $attribute    = $this->attributeRepository->get('brands');

            if ($attribute && $attribute->usesSource()) {
                    $attribute->setData('option', array('value' => array(
                        $option_id => array($englishLabel, $arabicLabel, $englishLabel)), 
                    ));
                    $attribute->save();
            }
        }
        return true;
    }

    public function deleteOptionById($optionId) {
        
            /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetupInterface]);
            $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE);           
            $attributeCode = 'brands';
            
            // IMPORTANT:
            // use $this->attributeFactory->create() before loading the attribute,
            // or else the options you want to delete will be cached and you cannot 
            // delete other options from a second attribute in the same request

            $attribute = $this->attributeFactory->create()->setStoreId(0)->loadByCode($entityTypeId, $attributeCode);
            $options = $attribute->getOptions();             
            $optionsToRemove = [];
            foreach($options as $option)
            { //echo $option['label'].$option['value'].'<br>'; //exit();
                if (isset($option['value']) && $option['value'] == $optionId)
                {
                    $optionsToRemove['delete'][$option['value']] = true;
                    $optionsToRemove['value'][$option['value']] = true;
                }
            }
            $eavSetup->addAttributeOption($optionsToRemove);    
        return true;
    }

}
