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


use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Ves\Brand\Model\BrandFactory;

/**
 * Class to resolve custom attribute_set_name field in brand GraphQL query
 */
class BrandAttributeSetImageResolver implements ResolverInterface
{
    /**
     * @var BrandFactory
     */
    private $brandFactory;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * BrandAttributeSetProductsResolver constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param BrandFactory $brand
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        BrandFactory $brand
    ) {
        $this->productRepository = $productRepository;
        $this->brandFactory = $brand;
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
        if (isset($value['image']) && isset($value['brand_id'])
            && $value['brand_id'] && $value['image']) {
            $brand = $this->brandFactory->create()->load($value['brand_id']);
            return $brand->getImageUrl();
        } else {
            return "";
        }
    }
}
