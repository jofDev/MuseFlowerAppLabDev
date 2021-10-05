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

namespace Lof\BrandGraphQl\Api\Data;

interface BrandSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get brand list.
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface[]
     */
    public function getItems();

    /**
     * Set brand_id list.
     * @param \Lof\BrandGraphQl\Api\Data\BrandInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
