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

namespace Lof\BrandGraphQl\Model;

use Lof\BrandGraphQl\Api\BrandRepositoryInterface;
use Lof\BrandGraphQl\Api\Data\BrandInterfaceFactory;
use Lof\BrandGraphQl\Api\Data\BrandSearchResultsInterfaceFactory;
use Magento\CatalogGraphQl\Model\Resolver\Products\DataProvider\ProductSearch\ProductCollectionSearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\ResourceConnection;
use Ves\Brand\Model\ResourceModel\Brand as ResourceBrand;
use Ves\Brand\Model\BrandFactory;
use Ves\Brand\Model\ResourceModel\Brand\CollectionFactory as BrandCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Ves\Brand\Model\ResourceModel\Group\CollectionFactory;

/**
 * Class BrandRepository
 * @package Lof\BrandGraphQl\Model
 */
class BrandRepository implements BrandRepositoryInterface
{

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var BrandFactory
     */
    protected $brandFactory;

    /**
     * @var BrandInterfaceFactory
     */
    protected $dataBrandFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var ResourceBrand
     */
    protected $resource;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    /**
     * @var BrandSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var BrandCollectionFactory
     */
    protected $brandCollectionFactory;
    /**
     * @var ResourceConnection
     */
    private $_resourceConnection;
    /**
     * @var ProductCollectionSearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var CollectionFactory
     */
    private $groupCollectionFactory;


    /**
     * @param ResourceBrand $resource
     * @param BrandFactory $brandFactory
     * @param BrandInterfaceFactory $dataBrandFactory
     * @param BrandCollectionFactory $brandCollectionFactory
     * @param BrandSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param ResourceConnection $resourceConnection
     * @param ProductCollectionSearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionFactory $groupCollectionFactory
     */
    public function __construct(
        ResourceBrand $resource,
        BrandFactory $brandFactory,
        BrandInterfaceFactory $dataBrandFactory,
        BrandCollectionFactory $brandCollectionFactory,
        BrandSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        ResourceConnection $resourceConnection,
        ProductCollectionSearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionFactory $groupCollectionFactory
    ) {
        $this->resource = $resource;
        $this->brandFactory = $brandFactory;
        $this->brandCollectionFactory = $brandCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBrandFactory = $dataBrandFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->_resourceConnection = $resourceConnection;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->groupCollectionFactory = $groupCollectionFactory;

    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lof\BrandGraphQl\Api\Data\BrandInterface $brand
    ) {
        /* if (empty($brand->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $brand->setStoreId($storeId);
        } */

        $brandData = $this->extensibleDataObjectConverter->toNestedArray(
            $brand,
            [],
            \Lof\BrandGraphQl\Api\Data\BrandInterface::class
        );

        $brandModel = $this->brandFactory->create()->setData($brandData);

        try {
            $this->resource->save($brandModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the brand: %1',
                $exception->getMessage()
            ));
        }
        return $brandModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($brandId)
    {
        $brand = $this->brandFactory->create();
        $this->resource->load($brand, $brandId);
        if (!$brand->getId()) {
            throw new NoSuchEntityException(__('brand with id "%1" does not exist.', $brandId));
        }
        return $brand->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria,
        $search
    ) {
        $collection = $this->brandCollectionFactory->create();
        if($search!=""){
            $collection->addFieldToFilter(
                array(
                    'name',
                    'description',
                ),
                array(
                    array('like' => '%'.$search.'%'),
                    array('like' => '%'.$search.'%'),
                )
            );
        }
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\BrandGraphQl\Api\Data\BrandInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();

        $searchResults->setSearchCriteria($criteria);

        $searchResults->setItems($collection->getData());

        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * @param SearchCriteriaInterface $criteria
     * @param string $search
     * @return \Lof\BrandGraphQl\Api\Data\BrandSearchResultsInterface|mixed
     */
    public function getGroups(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria,
        $search
    ) {
        $collection = $this->groupCollectionFactory->create();
        if($search!=""){
            $collection->addFieldToFilter(
                array(
                    'name'
                ),
                array(
                    array('like' => '%'.$search.'%')
                )
            );
        }
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\BrandGraphQl\Api\Data\GroupInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $searchResults->setItems($collection->getData());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getListByProduct(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        $productId
    ) {
        $collection = $this->brandCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\BrandGraphQl\Api\Data\BrandInterface::class
        );

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        $connection = $this->_resourceConnection->getConnection();
        $table_name = $this->_resourceConnection->getTableName('ves_brand_product');
        $brandIds = $connection->fetchCol(" SELECT brand_id FROM ".$table_name." WHERE product_id = ".$productId);
        if($brandIds || count($brandIds) > 0) {
            $collection
                ->setOrder('position','ASC')
                ->addFieldToFilter('status',1);
            $collection->getSelect()->where('brand_id IN (?)', $brandIds);

            $searchResults->setItems($collection->getData());
            $searchResults->setTotalCount($collection->getSize());
        }

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lof\BrandGraphQl\Api\Data\BrandInterface $brand
    ) {
        try {
            $brandModel = $this->brandFactory->create();
            $this->resource->load($brandModel, $brand->getBrandId());
            $this->resource->delete($brandModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the brand: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($brandId)
    {
        return $this->delete($this->get($brandId));
    }

    /**
     * Format sort orders into associative array
     *
     * E.g. ['field1' => 'DESC', 'field2' => 'ASC", ...]
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return array
     */
    private function getSortOrderArray(SearchCriteriaInterface $searchCriteria)
    {
        $ordersArray = [];
        $sortOrders = $searchCriteria->getSortOrders();
        if (is_array($sortOrders)) {
            foreach ($sortOrders as $sortOrder) {
                // I am replacing _id with entity_id because in ElasticSearch _id is required for sorting by ID.
                // Where as entity_id is required when using ID as the sort in $collection->load();.
                // @see \Magento\CatalogGraphQl\Model\Resolver\Products\Query\Search::getResult
                if ($sortOrder->getField() === '_id') {
                    $sortOrder->setField('entity_id');
                }
                $ordersArray[$sortOrder->getField()] = $sortOrder->getDirection();
            }
        }

        return $ordersArray;
    }
}
