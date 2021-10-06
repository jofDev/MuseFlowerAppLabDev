<?php

namespace Vnecoms\Sms\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Review\Model\Rating;


class ReviewMessageSaveBefore implements ObserverInterface
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Email\Model\Template\Filter
     */
    protected $filter;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productloader;

    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param \Magento\Email\Model\Template\Filter $filter
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Email\Model\Template\Filter $filter,
        \Magento\Catalog\Model\ProductFactory $_productloader
    )
    {
        $this->helper = $helper;
        $this->filter = $filter;
        $this->_productloader = $_productloader;
    }

    /**
     * Vendor Save After
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->helper->getCurrentGateway()) return;
        
        $review = $observer->getDataObject();
        $product = $this->_productloader->create()->load($review->getData('entity_pk_value'));

        /* Send notification message to admin when a new review message sent*/
        if ($this->helper->canSendNewReviewMessageToAdmin()) {
            $message = $this->helper->getNewReviewMessageSendToAdmin();
            $this->filter->setVariables([
                'product' => $product,
                'nickname' => $review->getData('nickname'),
                'title' => $review->getData('title'),
                'detail' => $review->getData('detail'),
            ]);
            $message = $this->filter->filter($message);
            $this->helper->sendAdminSms($message);
        }
    }

}
