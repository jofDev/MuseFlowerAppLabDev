define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/quote',
        'jquery',
        'ko',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/action/set-payment-information',
        'mage/url',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Ui/js/model/messageList',
		'Magento_Ui/js/modal/modal'
    ],
    function (Component, quote, $, ko, additionalValidators, setPaymentInformationAction, url, customer, placeOrderAction, fullScreenLoader, messageList,modal) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Sadadqar_Magento/payment/sadadqa-form.phtml',
				redirectAfterPlaceOrder: false
            },
            getMerchantName: function() {
                return window.checkoutConfig.payment.sadadqa.key_secret;
            },

            getKeyId: function() {
                return window.checkoutConfig.payment.sadadqa.key_id;
            },
			

            context: function() {
                return this;
            },

            isShowLegend: function() {
                return true;
            },

            getCode: function() {
                return 'sadadqa';
            },

            isActive: function() {
                return true;
            },
			 afterPlaceOrder: function () {
				 fullScreenLoader.startLoader();
				 $.ajax({
                    type: 'POST',
                    url: url.build('sadadqa/payment/order')+'?redirct=1',
                    data: {},

                    /**
                     * Success callback
                     * @param {Object} response
                     */
                    success: function (response) {
                        fullScreenLoader.stopLoader();
                      	$('#checkout').html(response);  
						if ($('#showdialog').val() == 1) {
                                
                                      $('#exampleModal').show();
                                      $('#paymentform').attr('target', 'includeiframe').submit();
                                      $('#onlyiframe').remove();
                                    
                                } else {
                                     $('#exampleModal').remove();
                                     $('#paymentform').attr('target', 'onlyiframe').submit();
                                    
                                }
                    },


                    /**
                     * Error callback
                     * @param {*} response
                     */
                    error: function (response) {
                        fullScreenLoader.stopLoader();
                    }
                });
				 
            	//window.location.href=url.build('sadadqa/payment/order')+'?redirct=1';
				
				return false;
			},

            handleError: function (error) {
                if (_.isObject(error)) {
                    this.messageContainer.addErrorMessage(error);
                } else {
                    this.messageContainer.addErrorMessage({
                        message: error
                    });
                }
            },

            initObservable: function() {
                var self = this._super();

                return self;
            },

        });
    }


);
