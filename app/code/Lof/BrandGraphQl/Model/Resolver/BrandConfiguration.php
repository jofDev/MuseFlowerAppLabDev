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

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Ves\Brand\Helper\Data as brandHelper;

/**
 * Resolve Store Config information for Brand
 */
class BrandConfiguration implements ResolverInterface
{
    /**
     * @var brandHelper
     */
    private $brandHelper;
    /**
     * @param brandHelper $brandHelper
     */
    public function __construct(brandHelper $brandHelper)
    {
        $this->brandHelper = $brandHelper;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $store = $context->getExtensionAttributes()->getStore();
        $storeId = $store->getId();

        return [
            'general_settings_enable'=> $this->brandHelper->getconfig('general_settings/enable',$storeId),
            'general_settings_route'=> $this->brandHelper->getconfig('general_settings/route',$storeId),
            'general_settings_url_prefix'=> $this->brandHelper->getconfig('general_settings/url_prefix',$storeId),
            'general_settings_url_suffix'=> $this->brandHelper->getconfig('general_settings/url_suffix',$storeId),
            'general_settings_enable_menu'=> $this->brandHelper->getconfig('general_settings/enable_menu',$storeId),
            'general_settings_enable_search'=> $this->brandHelper->getconfig('general_settings/enable_search',$storeId),
            'product_view_page_enable_brand_info'=> $this->brandHelper->getconfig('product_view_page/enable_brand_info',$storeId),
            'product_view_page_brand_layout_listing'=> $this->brandHelper->getconfig('product_view_page/brand_layout_listing',$storeId),
            'product_view_page_show_brand_text'=> $this->brandHelper->getconfig('product_view_page/show_brand_text',$storeId),
            'product_view_page_show_brand_description'=> $this->brandHelper->getconfig('product_view_page/show_brand_description',$storeId),
            'product_view_page_brand_text'=> $this->brandHelper->getconfig('product_view_page/brand_text',$storeId),
            'product_view_page_show_brand_image'=> $this->brandHelper->getconfig('product_view_page/show_brand_image',$storeId),
            'product_view_page_show_brand_name'=> $this->brandHelper->getconfig('product_view_page/show_brand_name',$storeId),
            'brand_list_page_layout'=> $this->brandHelper->getconfig('brand_list_page/layout',$storeId),
            'brand_list_page_show_brand_name'=> $this->brandHelper->getconfig('brand_list_page/show_brand_name',$storeId),
            'brand_list_page_item_per_page'=> $this->brandHelper->getconfig('brand_list_page/item_per_page',$storeId),
            'brand_list_page_seo_config_heading'=> $this->brandHelper->getconfig('brand_list_page/seo_config_heading',$storeId),
            'brand_list_page_page_title'=> $this->brandHelper->getconfig('brand_list_page/page_title',$storeId),
            'brand_list_page_meta_description'=> $this->brandHelper->getconfig('brand_list_page/meta_description',$storeId),
            'brand_list_page_meta_keywords'=> $this->brandHelper->getconfig('brand_list_page/meta_keywords',$storeId),
            'brand_list_page_grid_config_heading'=> $this->brandHelper->getconfig('brand_list_page/grid_config_heading',$storeId),
            'brand_list_page_lg_column_item'=> $this->brandHelper->getconfig('brand_list_page/lg_column_item',$storeId),
            'brand_list_page_md_column_item'=> $this->brandHelper->getconfig('brand_list_page/md_column_item',$storeId),
            'brand_list_page_sm_column_item'=> $this->brandHelper->getconfig('brand_list_page/sm_column_item',$storeId),
            'brand_list_page_xs_column_item'=> $this->brandHelper->getconfig('brand_list_page/xs_column_item',$storeId),
            'brand_block_enable'=> $this->brandHelper->getconfig('brand_block/enable',$storeId),
            'brand_block_pretext'=> $this->brandHelper->getconfig('brand_block/pretext',$storeId),
            'brand_block_brand_groups'=> $this->brandHelper->getconfig('brand_block/brand_groups',$storeId),
            'brand_block_show_brand_name'=> $this->brandHelper->getconfig('brand_block/show_brand_name',$storeId),
            'brand_block_number_item'=> $this->brandHelper->getconfig('brand_block/number_item',$storeId),
            'brand_block_addition_class'=> $this->brandHelper->getconfig('brand_block/addition_class',$storeId),
            'brand_block_carousel_layout'=> $this->brandHelper->getconfig('brand_block/carousel_layout',$storeId),
            'brand_block_number_item_per_column'=> $this->brandHelper->getconfig('brand_block/number_item_per_column',$storeId),
            'brand_block_mobile_items'=> $this->brandHelper->getconfig('brand_block/mobile_items',$storeId),
            'brand_block_tablet_small_items'=> $this->brandHelper->getconfig('brand_block/tablet_small_items',$storeId),
            'brand_block_tablet_items'=> $this->brandHelper->getconfig('brand_block/tablet_items',$storeId),
            'brand_block_portrait_items'=> $this->brandHelper->getconfig('brand_block/portrait_items',$storeId),
            'brand_block_default_items'=> $this->brandHelper->getconfig('brand_block/default_items',$storeId),
            'brand_block_large_items'=> $this->brandHelper->getconfig('brand_block/large_items',$storeId),
            'brand_block_autoplay'=> $this->brandHelper->getconfig('brand_block/autoplay',$storeId),
            'brand_block_autoplay_timeout'=> $this->brandHelper->getconfig('brand_block/autoplay_timeout',$storeId),
            'brand_block_autoplay_pauseonhover'=> $this->brandHelper->getconfig('brand_block/autoplay_pauseonhover',$storeId),
            'brand_block_item_per_page'=> $this->brandHelper->getconfig('brand_block/enable',$storeId),
            'brand_block_lg_column_item'=> $this->brandHelper->getconfig('brand_block/lg_column_item',$storeId),
            'brand_block_md_column_item'=> $this->brandHelper->getconfig('brand_block/md_column_item',$storeId),
            'brand_block_sm_column_item'=> $this->brandHelper->getconfig('brand_block/sm_column_item',$storeId),
            'brand_block_xs_column_item'=> $this->brandHelper->getconfig('brand_block/xs_column_item',$storeId),
            'brand_block_interval'=> $this->brandHelper->getconfig('brand_block/interval',$storeId),
            'brand_block_loop'=> $this->brandHelper->getconfig('brand_block/loop',$storeId),
            'brand_block_rtl'=> $this->brandHelper->getconfig('brand_block/rtl',$storeId),
            'brand_block_dots'=> $this->brandHelper->getconfig('brand_block/dots',$storeId),
            'brand_block_nav'=> $this->brandHelper->getconfig('brand_block/nav',$storeId),
            'brand_block_nav_prev'=> $this->brandHelper->getconfig('brand_block/nav_prev',$storeId),
            'brand_block_nav_next'=> $this->brandHelper->getconfig('brand_block/nav_next',$storeId),

            'group_page_show_brand_name'=> $this->brandHelper->getconfig('group_page/show_brand_name',$storeId),
            'group_page_item_per_page'=> $this->brandHelper->getconfig('group_page/item_per_page',$storeId),
            'group_page_lg_column_item'=> $this->brandHelper->getconfig('group_page/lg_column_item',$storeId),
            'group_page_md_column_item'=> $this->brandHelper->getconfig('group_page/md_column_item',$storeId),
            'group_page_sm_column_item'=> $this->brandHelper->getconfig('group_page/sm_column_item',$storeId),
            'group_page_xs_column_item'=> $this->brandHelper->getconfig('group_page/xs_column_item',$storeId),

        ];
    }
}
