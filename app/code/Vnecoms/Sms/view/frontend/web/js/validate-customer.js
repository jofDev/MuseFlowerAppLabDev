define([
    'jquery',
    'mage/mage',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/alert',
    'mage/validation/validation'
], function ($, mage, $t, modal, alert) {
    'use strict';

    $.widget('mage.customerLoginValidate', {
        options: {
            loginButtonSelector: '#login-form .action.login',
            loadingBoxSelector: '#ves-loadingbox',
            otpDialogSelector: '#sms-otp-dialog',
            sendOtpURL: '',
            verifyOtpURL: '',
        },
        mobileNumber: '',
        secureKey: '',

        /**
         * Uses Magento's validation widget for the form object.
         * @private
         */
        _create: function () {
            this.initValidation();
            this.initOtpForm();
        },
        /**
         * Init OTP Form
         */
        initOtpForm: function(){
            var options = {
                type: 'popup',
                modalClass:'otp-verify-modal',
                responsive: true,
                innerScroll: true,
                title: $.mage.__('2-Step Verification'),
                buttons: []
            };
            var popup = modal(options, $(this.options.otpDialogSelector));

            $('#send-otp-btn').click(function(){
                this.sendOtp(0);
            }.bind(this));

            $('#resend-otp-btn').click(function(){
                if($('#resend-otp-btn').hasClass('running')){
                    return false;
                }
                this.sendOtp(1);
                return false;
            }.bind(this));

            $('#verify-otp-btn').click(function(){
                this.verifyOtp();
            }.bind(this));
        },
        /**
         * Init validation
         */
        initValidation: function(){
            var self = this;
            this.element.validation({
                /**
                 * Uses catalogAddToCart widget as submit handler.
                 * @param {Object} form
                 * @returns {Boolean}
                 */
                submitHandler: function (form) {
                    var formData = new FormData($(form)[0]);
                    $(self.options.loadingBoxSelector).addClass('show');
                    $.ajax({
                        url: $(form).data('test_login_action'),
                        data: formData,
                        type: 'post',
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,

                        /** @inheritdoc */
                        success: function (res) {
                            $(self.options.loadingBoxSelector).removeClass('show');
                            console.log(res);
                            if(res.success){
                                self.mobileNumber = res.mobilenumber;
                                self.secureKey = res.secure_key;
                                $('#sms-otp-dialog-mobile').html(res.mobilenumber);
                                self.openOtpForm();
                            }
                        },

                        /** @inheritdoc */
                        error: function (res) {
                            $(self.options.loadingBoxSelector).removeClass('show');
                        },
                    });
                    return false;
                }
            });
            $(this.options.loginButtonSelector).attr('disabled', false);
        },
        /**
         * Send otp
         */
        sendOtp: function (isResend){
            $('#send-otp-btn').prop('disabled', true);
            $.ajax({
                url: this.options.sendOtpURL,
                method: "POST",
                data: {
                    mobile : this.mobileNumber,
                    secure_key: this.secureKey,
                    resend : isResend,
                },
                dataType: "json"
            }).done(function( response ){
                $('#send-otp-btn').prop('disabled', false);
                if(response.success){
                    this.openVerifyOtpForm();
                    this.runCountDown();
                }else{
                    alert({
                        modalClass: 'confirm ves-error',
                        title: $.mage.__("Verify Error"),
                        content: response.msg,
                    });
                }

            }.bind(this));
        },

        runCountDown: function (){
            var resendBtn = $('#resend-otp-btn');
            if(!resendBtn.hasClass('running')){
                resendBtn.addClass('running');
            }

            if(!resendBtn.data('couting')){
                !resendBtn.data('couting', resendBtn.data('time'));
            }
            var count = parseInt(resendBtn.data('couting'));
            count --;
            resendBtn.data('couting', count);
            resendBtn.html($.mage.__('Resend after %1 seconds').replace('%1', count));

            if(count == 0) {
                resendBtn.removeClass('running');
                resendBtn.html($.mage.__('Resend OTP'));
                return;
            }
            setTimeout(function(){this.runCountDown()}.bind(this), 1000);
        },

        /**
         * Verify OTP
         */
        verifyOtp: function(){
            $('#sms-otp-error').remove();
            if($('#verify-otp-btn').hasClass('verifying')){
                return;
            }
            var otp = $('#sms-otp-input').val();
            if(!otp){
                $('#sms-otp-input').after('<div id="sms-otp-error" class="sms-otp-error" for="sms-otp-input"><?php echo __("This is required field.");?></div>');
                return;
            }

            $('#verify-otp-btn').addClass('verifying');

            $.ajax({
                url: this.options.verifyOtpURL,
                method: "POST",
                data: {
                    mobile : this.mobileNumber,
                    secure_key: this.secureKey,
                    otp: otp
                },
                dataType: "json"
            }).done(function( response ){
                if(response.success){
                    window.location.reload();
                }else{
                    $('#verify-otp-btn').removeClass('verifying');
                    $('#sms-otp-input').val('');
                    $('#sms-otp-input').after('<div id="sms-otp-error" class="sms-otp-error" for="sms-otp-input">'+response.msg+'</div>');
                }

            });
        },
        /**
         * Open OTP Form
         */
        openOtpForm: function(){
            $('.sms-otp-step-1').show();
            $('.sms-otp-step-2').hide();
            $('#sms-otp-dialog').modal('openModal');
        },

        /**
         * Open Verify OTP Form
         */
        openVerifyOtpForm: function(){
            $('.sms-otp-step-1').hide();
            $('.sms-otp-step-2').show();
            $('#mobile-number-id').val(this.mobileNumber);
        }
    });

    return $.mage.customerLoginValidate;
});
