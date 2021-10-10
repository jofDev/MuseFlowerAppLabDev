define([
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Checkout/js/model/quote',
    'uiRegistry',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/alert'
], function ($, Element, quote, registry, $t, modal, alert) {
	return Element.extend({
        SOURCE_CUSTOMER: 'customer',
        SOURCE_BILLING: 'billing',
        SOURCE_SHIPPING: 'shipping',

		defaults:{
            template: 'Vnecoms_Sms/checkout/payment/otp-form',
            otpResendPeriodTime: 30,
            otpLength: 6,
            processing: false,
            customerMobileNumber: '',
            mobileSource:{},
            defaultResendBtnLabel: ''
		},

		initialize: function () {
            this._super();
            return this;
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Abstract} Chainable.
         */
        initObservable: function () {
            this._super();
            this.observe('otp sentOtp otpValidated countNum resendBtnLabel mobilenumber processing');
            return this;
        },
        /**
         * Get Mobile number
         *
         * @returns {string}
         */
        getMobileNumber: function(){
            var mobile = '';
            for(var index in this.mobileSource){
                switch(this.mobileSource[index]){
                    case this.SOURCE_CUSTOMER:
                        mobile = this.customerMobileNumber;
                        break;
                    case this.SOURCE_BILLING:
                        mobile = quote.billingAddress()?quote.billingAddress().telephone:'';
                        break;
                    case this.SOURCE_SHIPPING:
                        mobile = quote.shippingAddress()?quote.shippingAddress().telephone:'';
                        break;
                }
                if(mobile) break;
            }

            return mobile;
        },
        initDialog: function(){
            var options = {
                type: 'popup',
                modalClass:'otp-verify-modal',
                responsive: true,
                innerScroll: true,
                title: $t('Verify Your Mobile Number'),
                buttons: []
            };
            var popup = modal(options, $('#sms-otp-dialog'));
        },

        getOtp: function(isResend = 0){
            if(this.processing()) return;
		    this.countNum(false);
            var self = this;
            this.processing(true);
            $.ajax({
                url: this.sendOtpUrl,
                method: "POST",
                data: {
                    mobile : this.getMobileNumber(),
                    resend : isResend,
                },
                dataType: "json"
            }).done(function( response ){
                self.processing(false);
                if(response.success){
                    self.sentOtp(true);
                    self.runCountDown();
                }else{
                    alert({
                        modalClass: 'confirm ves-error',
                        title: $t("Verify Error"),
                        content: response.msg,
                    });
                }
            });
        },

        resendOtp: function(){
            if(this.countNum() !== false || this.processing()) return;
            this.getOtp(1);
        },

        /**
         * Run Count Down
         */
        runCountDown: function(){
            if(this.countNum() === false){
                this.countNum(this.otpResendPeriodTime);
            }
            var count = this.countNum();
            count --;
            this.resendBtnLabel($t('Resend (%1)').replace('%1', count));
            if(count == 0) {
                this.resendBtnLabel(this.defaultResendBtnLabel);
                this.countNum(false);
                return;
            }
            this.countNum(count);
            setTimeout(function(){this.runCountDown()}.bind(this), 1000);
        },

        /**
         * Verify Otp
         */
        verifyOtp: function(){
            $.ajax({
                url: this.verifyOtpUrl,
                method: "POST",
                data: {
                    mobile : this.getMobileNumber(),
                    otp: this.otp()
                },
                dataType: "json"
            }).done(function(response){
                this.otp('');
                if(response.success){
                    this.otpValidated(true);
                    var methodObj = registry.get('checkout.steps.billing-step.payment.payments-list.'+quote.paymentMethod().method);
                    methodObj.placeOrder();
                    $('#sms-otp-dialog').modal('closeModal');
                    this.error('');
                }else{
                    alert({
                        modalClass: 'confirm ves-error',
                        title: $t("Verify Error"),
                        content: response.msg,
                    });
                }

            }.bind(this));
        },
	});
});
