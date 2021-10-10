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

namespace Lof\BrandGraphQl\Model\Data;

use Lof\BrandGraphQl\Api\Data\BrandInterface;

class Brand extends \Magento\Framework\Api\AbstractExtensibleObject implements BrandInterface
{

    /**
     * Get brand_id
     * @return string|null
     */
    public function getBrandId()
    {
        return $this->_get(self::BRAND_ID);
    }

    /**
     * Set brand_id
     * @param string $brandId
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setBrandId($brandId)
    {
        return $this->setData(self::BRAND_ID, $brandId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\BrandGraphQl\Api\Data\BrandExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lof\BrandGraphQl\Api\Data\BrandExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\BrandGraphQl\Api\Data\BrandExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get url_key
     * @return string|null
     */
    public function getUrlKey()
    {
        return $this->_get(self::URL_KEY);
    }

    /**
     * Set url_key
     * @param string $urlKey
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData(self::URL_KEY, $urlKey);
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get group_id
     * @return string|null
     */
    public function getGroupId()
    {
        return $this->_get(self::GROUP_ID);
    }

    /**
     * Set group_id
     * @param string $groupId
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    /**
     * Get image
     * @return string|null
     */
    public function getImage()
    {
        return $this->_get(self::IMAGE);
    }

    /**
     * Set image
     * @param string $image
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * Get thumbnail
     * @return string|null
     */
    public function getThumbnail()
    {
        return $this->_get(self::THUMBNAIL);
    }

    /**
     * Set thumbnail
     * @param string $thumbnail
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setThumbnail($thumbnail)
    {
        return $this->setData(self::THUMBNAIL, $thumbnail);
    }

    /**
     * Get page_title
     * @return string|null
     */
    public function getPageTitle()
    {
        return $this->_get(self::PAGE_TITLE);
    }

    /**
     * Set page_title
     * @param string $pageTitle
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setPageTitle($pageTitle)
    {
        return $this->setData(self::PAGE_TITLE, $pageTitle);
    }

    /**
     * Get meta_keywords
     * @return string|null
     */
    public function getMetaKeywords()
    {
        return $this->_get(self::META_KEYWORDS);
    }

    /**
     * Set meta_keywords
     * @param string $metaKeywords
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setMetaKeywords($metaKeywords)
    {
        return $this->setData(self::META_KEYWORDS, $metaKeywords);
    }

    /**
     * Get meta_description
     * @return string|null
     */
    public function getMetaDescription()
    {
        return $this->_get(self::META_DESCRIPTION);
    }

    /**
     * Set meta_description
     * @param string $metaDescription
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setMetaDescription($metaDescription)
    {
        return $this->setData(self::META_DESCRIPTION, $metaDescription);
    }

    /**
     * Get creation_time
     * @return string|null
     */
    public function getCreationTime()
    {
        return $this->_get(self::CREATION_TIME);
    }

    /**
     * Set creation_time
     * @param string $creationTime
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * Get update_time
     * @return string|null
     */
    public function getUpdateTime()
    {
        return $this->_get(self::UPDATE_TIME);
    }

    /**
     * Set update_time
     * @param string $updateTime
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * Get page_layout
     * @return string|null
     */
    public function getPageLayout()
    {
        return $this->_get(self::PAGE_LAYOUT);
    }

    /**
     * Set page_layout
     * @param string $pageLayout
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setPageLayout($pageLayout)
    {
        return $this->setData(self::PAGE_LAYOUT, $pageLayout);
    }

    /**
     * Get layout_update_xml
     * @return string|null
     */
    public function getLayoutUpdateXml()
    {
        return $this->_get(self::LAYOUT_UPDATE_XML);
    }

    /**
     * Set layout_update_xml
     * @param string $layoutUpdateXml
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setLayoutUpdateXml($layoutUpdateXml)
    {
        return $this->setData(self::LAYOUT_UPDATE_XML, $layoutUpdateXml);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get featured
     * @return string|null
     */
    public function getFeatured()
    {
        return $this->_get(self::FEATURED);
    }

    /**
     * Set featured
     * @param string $featured
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setFeatured($featured)
    {
        return $this->setData(self::FEATURED, $featured);
    }

    /**
     * Get position
     * @return string|null
     */
    public function getPosition()
    {
        return $this->_get(self::POSITION);
    }

    /**
     * Set position
     * @param string $position
     * @return \Lof\BrandGraphQl\Api\Data\BrandInterface
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
    }
}
