<?php
namespace Applab\QrCode\Helper;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Applab\QrCode\Lib\PHPQRCode\QRcode;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	public function __construct(
		\Magento\Framework\Filesystem $filesystem
	)
    {
       	$this->_filesystem = $filesystem;
    }

	public function generateQrCode($inputString, $imageName){

		$mediapath 	= $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
		$filepath 	= $mediapath.'logo/'.$imageName.".png";
		$ecc 		= 'L';
		$pixel_Size = 10;
		$frame_Size = 10;
		QRcode::png($inputString, $filepath, $ecc, $pixel_Size, $frame_Size);
		return $filepath;

	}


}