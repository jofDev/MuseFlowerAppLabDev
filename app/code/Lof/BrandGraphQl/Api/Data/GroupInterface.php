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
 * Interface GroupInterface
 * @package Lof\BrandGraphQl\Api\Data
 */
interface GroupInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    /**
     *
     */
    const GROUP_ID = 'group_id';
    /**
     *
     */
    const SHOW_IN_SIDEBAR = 'show_in_sidebar';
    /**
     *
     */
    const URL_JEY = 'url_jey';
    /**
     *
     */
    const POSITION = 'position';
    /**
     *
     */
    const STATUS = 'status';
    /**
     *
     */
    const NAME = 'name';

    /**
     * Get group_id
     * @return string|null
     */
    public function getGroupId();

    /**
     * Set group_id
     * @param string $groupId
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setGroupId($groupId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\BrandGraphQl\Api\Data\GroupExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\BrandGraphQl\Api\Data\GroupExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\BrandGraphQl\Api\Data\GroupExtensionInterface $extensionAttributes
    );

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setName($name);

    /**
     * Get url_jey
     * @return string|null
     */
    public function getUrlJey();

    /**
     * Set url_jey
     * @param string $urlJey
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setUrlJey($urlJey);

    /**
     * Get position
     * @return string|null
     */
    public function getPosition();

    /**
     * Set position
     * @param string $position
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setPosition($position);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setStatus($status);

    /**
     * Get show_in_sidebar
     * @return string|null
     */
    public function getShowInSidebar();

    /**
     * Set show_in_sidebar
     * @param string $showInSidebar
     * @return \Lof\BrandGraphQl\Api\Data\GroupInterface
     */
    public function setShowInSidebar($showInSidebar);
}
