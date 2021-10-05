<?php
/**
 * ScandiPWA - Progressive Web App for Magento
 *
 * Copyright © Scandiweb, Inc. All rights reserved.
 * See LICENSE for license details.
 *
 * @license OSL-3.0 (Open Software License ("OSL") v. 3.0)
 * @package scandipwa/quote-graphql
 * @link    https://github.com/scandipwa/quote-graphql
 */
declare(strict_types=1);

namespace ScandiPWA\QuoteGraphQl\Model\Resolver;

use Magento\Quote\Api\CartManagementInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\QuoteGraphQl\Model\Cart\CheckCartCheckoutAllowance;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Magento\QuoteGraphQl\Model\Cart\SetBillingAddressOnCart as SetBillingAddressOnCartModel;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Mutation resolver for setting payment method for shopping cart
 */
class SetBillingAddressOnCart implements ResolverInterface
{
    /** @var CartManagementInterface  */
    protected $cartManagement;

    /** @var GetCartForUser  */
    protected $getCartForUser;

    /** @var SetBillingAddressOnCartModel  */
    protected $setBillingAddressOnCart;

    /** @var CheckCartCheckoutAllowance  */
    protected $checkCartCheckoutAllowance;

    /** @var CustomerRepositoryInterface */
    protected $customerRepository;

    /**
     * @param GetCartForUser $getCartForUser
     * @param CartManagementInterface $cartManagement
     * @param SetBillingAddressOnCartModel $setBillingAddressOnCart
     * @param CheckCartCheckoutAllowance $checkCartCheckoutAllowance
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        GetCartForUser $getCartForUser,
        CartManagementInterface $cartManagement,
        SetBillingAddressOnCartModel $setBillingAddressOnCart,
        CheckCartCheckoutAllowance $checkCartCheckoutAllowance,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->getCartForUser = $getCartForUser;
        $this->cartManagement = $cartManagement;
        $this->setBillingAddressOnCart = $setBillingAddressOnCart;
        $this->checkCartCheckoutAllowance = $checkCartCheckoutAllowance;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $guestCartId = $args['input']['guest_cart_id'] ?? '';

        if (empty($args['input']['billing_address'])) {
            throw new GraphQlInputException(__('Required parameter "billing_address" is missing'));
        }
        $billingAddress = $args['input']['billing_address'];
        $customerAddressId = isset($billingAddress['customer_address_id'])
            ? $billingAddress['customer_address_id']
            : null;
        $sameAsShipping = !!(isset($args['input']['same_as_shipping']) && $args['input']['same_as_shipping']);

        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();

        $customerId = $context->getUserId();
        if ($guestCartId !== '') {
            $cart = $this->getCartForUser->execute($guestCartId, $customerId, $storeId);
        } else {
            $cart = $this->cartManagement->getCartForCustomer($customerId);
        }

        $this->checkCartCheckoutAllowance->execute($cart);
        $billingAddressIsSaved = false;

        if ($sameAsShipping && $customerAddressId) {
            $customer = $cart->getCustomer();
            $billingAddressId = $customer->getDefaultBilling();

            if (!$billingAddressId) {
                $customer->setDefaultBilling((string)$customerAddressId);
                $this->customerRepository->save($customer);
                $billingAddressIsSaved = true;
            } else {
                unset($billingAddress['customer_address_id']);
            }
        }

         if (!$billingAddressIsSaved) {
            $this->setBillingAddressOnCart->execute($context, $cart, $billingAddress);
        }

        return [
            'cart' => [
                'model' => $cart,
            ],
        ];
    }
}
