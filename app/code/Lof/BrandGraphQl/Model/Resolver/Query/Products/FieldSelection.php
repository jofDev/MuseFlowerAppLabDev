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
declare(strict_types=1);

namespace Lof\BrandGraphQl\Model\Resolver\Query\Products;

use Magento\Framework\GraphQl\Query\FieldTranslator;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Extract requested fields from products query
 */
class FieldSelection
{
    /**
     * @var FieldTranslator
     */
    private $fieldTranslator;

    /**
     * @param FieldTranslator $fieldTranslator
     */
    public function __construct(FieldTranslator $fieldTranslator)
    {
        $this->fieldTranslator = $fieldTranslator;
    }

    /**
     * Get requested fields from products query
     *
     * @param ResolveInfo $resolveInfo
     * @return string[]
     */
    public function getProductsFieldSelection(ResolveInfo $resolveInfo): array
    {
        $productFields = $resolveInfo->getFieldSelection(1);
        $sectionNames = ['items', 'product'];

        $fieldNames = [];
        foreach ($sectionNames as $sectionName) {
            if (isset($productFields[$sectionName])) {
                foreach (array_keys($productFields[$sectionName]) as $fieldName) {
                    $fieldNames[] = $this->fieldTranslator->translate($fieldName);
                }
            }
        }

        return array_unique($fieldNames);
    }
}
