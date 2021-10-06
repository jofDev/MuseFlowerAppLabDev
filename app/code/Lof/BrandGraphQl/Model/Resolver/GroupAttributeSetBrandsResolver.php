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

use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Ves\Brand\Model\ResourceModel\Brand\CollectionFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class to resolve custom attribute_set_name field in group GraphQL query
 */
class GroupAttributeSetBrandsResolver implements ResolverInterface
{

    /**
     * @var CollectionFactory
     */
    private $brandCollectionFactory;

    /**
     * GroupAttributeSetBrandsResolver constructor.
     * @param CollectionFactory $brandCollectionFactory
     */
    public function __construct(
        CollectionFactory $brandCollectionFactory
    ) {
        $this->brandCollectionFactory = $brandCollectionFactory;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (isset($value['group_id']) && $value['group_id']) {
            $collection = $this->brandCollectionFactory->create();
            $collection->addFieldToFilter('group_id', $value['group_id']);
            $items = [];
            foreach($collection as $item) {
                $item->load($item->getBrandId());
                $items[] = $item;
            }
            return [
                'total_count' => $collection->getSize(),
                'items' => $collection->getData()
            ];
        } else {
            return [];
        }
    }
}
