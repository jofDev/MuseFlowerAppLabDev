<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Core\Block\Adminhtml\Key\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $licenseId = $this->getLicenseId();
        if ($licenseId && $this->canRender('delete')) {
            $data = [
                'label' => __('Delete License'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->urlBuilder->getUrl('*/*/delete', ['id' => $licenseId]) . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }
}
