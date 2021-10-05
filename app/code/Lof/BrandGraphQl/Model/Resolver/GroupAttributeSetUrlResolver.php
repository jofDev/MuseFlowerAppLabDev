<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_BrandGraphQl
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\BrandGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Model\StoreManagerInterface;
use Ves\Brand\Helper\Data;

/**
 * Class to resolve custom attribute_set_url field in group GraphQL query
 */
class GroupAttributeSetUrlResolver implements ResolverInterface
{

    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;
    /**
     * @var Data
     */
    private $_brandHelper;

    /**
     * BrandAttributeSetProductsResolver constructor.
     * @param StoreManagerInterface $storeManager
     * @param Data $helperData
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Data $helperData
    ) {
        $this->_storeManager = $storeManager;
        $this->_brandHelper = $helperData;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     * @throws NoSuchEntityException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (isset($value['url_key']) && $value['url_key']) {
            $url = $this->_storeManager->getStore()->getBaseUrl();
            $url_prefix = $this->_brandHelper->getConfig('general_settings/url_prefix');
            $urlPrefix = '';
            if($url_prefix){
                $urlPrefix = $url_prefix.'/';
            }
            $url_suffix = $this->_brandHelper->getConfig('general_settings/url_suffix');
            return $url.$urlPrefix.$value['url_key'].$url_suffix;
        } else {
            return "";
        }
    }
}
