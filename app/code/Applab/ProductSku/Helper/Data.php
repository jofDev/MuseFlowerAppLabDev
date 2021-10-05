<?php
namespace Applab\ProductSku\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	const SKU_PREFIX = 'sku_section/general/prefix';

	public function __construct(
		\Magento\Framework\App\Helper\Context $context
	) {
		parent::__construct($context);
	}	

	public function getConfig($path) {
		return $this->scopeConfig->getValue(
			$path,
			\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
	}

	public function getSkuPrefix(){	
		$prefix = $this->getConfig(self::SKU_PREFIX) ? str_replace(' ', '-', $this->getConfig(self::SKU_PREFIX)).'-' : '';
		return $prefix;
	}


}