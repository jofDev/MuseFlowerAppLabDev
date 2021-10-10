/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiRegistry',
    'Magento_Checkout/js/model/quote',
    'mage/validation'
], function ($, registry, quote) {
    'use strict';

    return {
        /**
         * Validate checkout agreements
         *
         * @returns {Boolean}
         */
        validate: function () {
            var otpForm = registry.get('checkout.steps.billing-step.payment.afterMethods.otp-validation-frm');
            if(!otpForm || otpForm.otpValidated()) return true;
            if(quote.paymentMethod()) {
                $('#sms-otp-dialog').modal('openModal');
            }
            return false;
        }
    };
});
