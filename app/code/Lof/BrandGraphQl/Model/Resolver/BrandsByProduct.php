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

namespace Lof\BrandGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lof\BrandGraphQl\Api\BrandRepositoryInterface;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;

class BrandsByProduct implements ResolverInterface
{

    private $brandDataProvider;
    /**
     * @var BrandRepositoryInterface
     */
    private $brandRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param DataProvider\Brand $brandDataProvider
     * @param BrandRepositoryInterface $brandRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        DataProvider\Brand $brandDataProvider,
        BrandRepositoryInterface $brandRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->brandRepository = $brandRepository;
        $this->brandDataProvider = $brandDataProvider;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }
        if (!$args['product_id']) {
            throw new GraphQlInputException(__('Product Id input is required.'));
        }
        $searchCriteria = $this->searchCriteriaBuilder->build( 'ves_brand', $args );
        $searchCriteria->setCurrentPage( $args['currentPage'] );
        $searchCriteria->setPageSize( $args['pageSize'] );

        $searchResult = $this->brandRepository->getListByProduct( $searchCriteria , $args['product_id']);

        return [
            'total_count' => $searchResult->getTotalCount(),
            'items'       => $searchResult->getItems(),
        ];
    }
}
