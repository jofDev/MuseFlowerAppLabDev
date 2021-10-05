<?php
namespace Vnecoms\Sms\Observer;

use Magento\Framework\Event\ObserverInterface;

class NotifyOrderCommentBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    
    /**
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry
    ){
        $this->coreRegistry = $coreRegistry;
    }
    
    /**
     * Vendor Save After
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getRequest();
        $data = $request->getPost('history');
        if (!isset($data['is_sms_notified']) || !$data['is_sms_notified']) return;
        
        $this->coreRegistry->register('is_sms_notified', true);
    }
}
