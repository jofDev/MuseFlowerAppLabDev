/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Vnecoms_Sms/js/model/otp-validator'
    ],
    function (Component, additionalValidators, agreementValidator) {
        'use strict';

        additionalValidators.registerValidator(agreementValidator);

        return Component.extend({});
    }
);
