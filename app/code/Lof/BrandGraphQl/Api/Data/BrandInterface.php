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

/**
 * Interface BrandInterface
 * @package Lof\BrandGraphQl\Api\Data
 */
interface BrandInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    /**
     *
     */
    const META_DESCRIPTION = 'meta_description';
    /**
     *
     */
    const GROUP_ID = 'group_id';
    /**
     *
     */
    const DESCRIPTION = 'description';
    /**
     *
     */
    const LAYOUT_UPDATE_XML = 'layout_update_xml';
    /**
     *
     */
    const CREATION_TIME = 'creation_time';
    /**
     *
     */
    const UPDATE_TIME = 'update_time';
    /**
     *
     */
    const POSITION = 'position';
    /**
     *
     */
    const FEATURED = 'featured';
    /**
     *
     */
    const PAGE_LAYOUT = 'page_layout';
    /**
     *
     */
    const THUMBNAIL = 'thumbnail';
    /**
     *
     */
    const PAGE_TITLE = 'page_title';
    /**
     *
     */
    const STATUS = 'status';
    /**
     *
     */
    const NAME = 'name';
    /**
     *
     */
    const BRAND_ID = 'brand_id';
    /**
     *
     */
    const IMAGE = 'image';
    /**
     *
     */
    const META_KEYWORDS = 'meta_keywords';
    /**
     *
     */
    const URL_KEY = 'url_key';

    /**
     * Get brand_id
     * @return string|null
     */
    public function getBrandId();

    /**
     * Set brand_id
     * @param string $brandId
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setBrandId($brandId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\BrandGraphQl\Api\Data\BrandExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\BrandGraphQl\Api\Data\BrandExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\BrandGraphQl\Api\Data\BrandExtensionInterface $extensionAttributes
    );

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setName($name);

    /**
     * Get url_key
     * @return string|null
     */
    public function getUrlKey();

    /**
     * Set url_key
     * @param string $urlKey
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setUrlKey($urlKey);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setDescription($description);

    /**
     * Get group_id
     * @return string|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param string $groupId
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setGroupId($groupId);

    /**
     * Get image
     * @return string|null
     */
    public function getImage();

    /**
     * Set image
     * @param string $image
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setImage($image);

    /**
     * Get thumbnail
     * @return string|null
     */
    public function getThumbnail();

    /**
     * Set thumbnail
     * @param string $thumbnail
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setThumbnail($thumbnail);

    /**
     * Get page_title
     * @return string|null
     */
    public function getPageTitle();

    /**
     * Set page_title
     * @param string $pageTitle
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setPageTitle($pageTitle);

    /**
     * Get meta_keywords
     * @return string|null
     */
    public function getMetaKeywords();

    /**
     * Set meta_keywords
     * @param string $metaKeywords
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * Get meta_description
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * Set meta_description
     * @param string $metaDescription
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * Get creation_time
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Set creation_time
     * @param string $creationTime
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Get update_time
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Set update_time
     * @param string $updateTime
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Get page_layout
     * @return string|null
     */
    public function getPageLayout();

    /**
     * Set page_layout
     * @param string $pageLayout
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setPageLayout($pageLayout);

    /**
     * Get layout_update_xml
     * @return string|null
     */
    public function getLayoutUpdateXml();

    /**
     * Set layout_update_xml
     * @param string $layoutUpdateXml
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setLayoutUpdateXml($layoutUpdateXml);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setStatus($status);

    /**
     * Get featured
     * @return string|null
     */
    public function getFeatured();

    /**
     * Set featured
     * @param string $featured
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setFeatured($featured);

    /**
     * Get position
     * @return string|null
     */
    public function getPosition();

    /**
     * Set position
     * @param string $position
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setPosition($position);
}
