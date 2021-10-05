<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\Core\Block\Adminhtml\Key\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class UpdateButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->canRender('update')) {
            $licenseId = $this->getLicenseId();
            $data = [
                'label' => __('Sync License'),
                'class' => 'save',
                'on_click' => 'setLocation(\'' . $this->urlBuilder->getUrl('*/*/load', ['id' => $licenseId]) . '\')',
            ];
        }
        return $data;
    }
}
