<?php
namespace Applab\Subscription\Plugin;

class SetCustomDataToOrderItem
{
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);
        $orderItem->setDeliveryDate($item->getDeliveryDate());
        $orderItem->setDeliveryTime($item->getDeliveryTime());
        $orderItem->setGiftQrcode($item->getGiftQrcode());
        return $orderItem;
    }
}