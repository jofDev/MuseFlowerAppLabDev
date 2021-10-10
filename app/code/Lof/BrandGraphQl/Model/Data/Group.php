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

use Lof\BrandGraphQl\Api\Data\GroupInterface;

class Group extends \Magento\Framework\Api\AbstractExtensibleObject implements GroupInterface
{

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
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\BrandGraphQl\Api\Data\GroupExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lof\BrandGraphQl\Api\Data\GroupExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\BrandGraphQl\Api\Data\GroupExtensionInterface $extensionAttributes
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
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get url_jey
     * @return string|null
     */
    public function getUrlJey()
    {
        return $this->_get(self::URL_JEY);
    }

    /**
     * Set url_jey
     * @param string $urlJey
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setUrlJey($urlJey)
    {
        return $this->setData(self::URL_JEY, $urlJey);
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
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setPosition($position)
    {
        return $this->setData(self::POSITION, $position);
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
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get show_in_sidebar
     * @return string|null
     */
    public function getShowInSidebar()
    {
        return $this->_get(self::SHOW_IN_SIDEBAR);
    }

    /**
     * Set show_in_sidebar
     * @param string $showInSidebar
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setShowInSidebar($showInSidebar)
    {
        return $this->setData(self::SHOW_IN_SIDEBAR, $showInSidebar);
    }
}
