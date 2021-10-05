<?php
namespace Vnecoms\Sms\Observer;

use Magento\Framework\Event\ObserverInterface;

class ContactMessagePost implements ObserverInterface
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
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Vnecoms\Sms\Model\MobileFactory
     */
    protected $mobileFactory;

    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param \Magento\Email\Model\Template\Filter $filter
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Vnecoms\Sms\Model\MobileFactory $mobileFactory
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Email\Model\Template\Filter $filter,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Vnecoms\Sms\Model\MobileFactory $mobileFactory
    ){
        $this->helper = $helper;
        $this->filter = $filter;
        $this->customerFactory = $customerFactory;
        $this->mobileFactory = $mobileFactory;
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
        
        $controller = $observer->getControllerAction();
        $request = $controller->getRequest();

        if (!$this->isPostRequest($request) || !$this->validatedParams($request)) {
            return;
        }

        /* Send notification message to admin when a new contact message sent*/
        if($this->helper->canSendNewContactMessageToAdmin()){
            $message = $this->helper->getNewContactMessageSendToAdmin();
            $this->filter->setVariables([
                'contact_name' => $request->getParam('name'),
                'contact_email' => $request->getParam('email'),
                'contact_telephone' => $request->getParam('telephone'),
                'comment' => $request->getParam('comment'),
            ]);
            $message = $this->filter->filter($message);
            $this->helper->sendAdminSms($message);
        }
    }

    /**
     * @param $request
     * @return bool
     */
    private function isPostRequest($request)
    {
        return !empty($request->getPostValue());
    }

    /**
     * @param $request
     * @return bool
     */
    private function validatedParams($request)
    {
        if (
            trim($request->getParam('name')) === '' ||
            trim($request->getParam('comment')) === '' ||
            false === \strpos($request->getParam('email'), '@') ||
            trim($request->getParam('hideit')) !== ''
        ) {
            return false;
        }

        return $request->getParams();
    }
}
