<?php
namespace Vnecoms\Sms\Plugin\Checkout;

use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;

class PlaceOrder
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * GuestPlaceOrder constructor.
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        CartRepositoryInterface $cartRepository
    ) {
        $this->helper = $helper;
        $this->cartRepository = $cartRepository;
    }


    /**
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $subject
     * @param int $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface|null $billingAddress
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        \Magento\Checkout\Api\PaymentInformationManagementInterface $subject,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if($this->helper->isEnableVerifyingAddressMobile()){
            /** @var Quote $quote */
            $quote = $this->cartRepository->getActive($cartId);

            if(!$quote->getData('otp_verified')){
                throw new LocalizedException(__('The OTP is not verified yet.'));
            }
        }
    }
}
