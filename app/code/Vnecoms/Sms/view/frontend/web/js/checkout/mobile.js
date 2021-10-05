define([
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Customer/js/model/address-list',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'jquery/intltellinput',
    'jquery/mask',
    'Vnecoms_Sms/js/utils'
], function ($, Element, addressList, $t, alert) {
	return Element.extend({
		defaults:{
            initialCountry: '',
            geoIpUrl: 'https://ipinfo.io',
			allowDropdown: '',
			onlyCountries: [],
            preferredCountries: [],
            otp: '',
            otpValidated: false,
            showOtpForm: false,
            isSentOtp: false,
            requireVerifying: false,
            sendOtpUrl: '',
            verifyOtpUrl: '',
            otpResendPeriodTime: 15,
            countNum: false,
            defaultResendBtnLabel: '',
            resendBtnLabel: '',
            customerMobileNumber: '',
            defaultMobile: '',
            listens: {
            	value: 'resetOtpValidation'
            }
		},
		
		initialize: function () {
            this._super();
            window.mobileObj = this;
            this.defaultMobile = this.customerMobileNumber;
            return this;
        },
        
        /**
         * Initializes observable properties of instance
         *
         * @returns {Abstract} Chainable.
         */
        initObservable: function () {
            this._super();
            this.observe('otp otpValidated showOtpForm isSentOtp countNum resendBtnLabel isSending isVerifying');

            return this;
        },
        
		/**
         * Init mobile input
         */
        initMobileInput: function(){
        	var self = this;
    	    var data= {
    	    	initialCountry: this.initialCountry,
				allowDropdown: this.allowDropdown == 'true' ? true : false,
	      		onlyCountries: this.onlyCountries,
	    		preferredCountries:this.preferredCountries
            }
    	    if(this.initialCountry == 'auto'){
    	    	data['geoIpLookup'] = function(callback) {
            	    $.get(self.geoIpUrl, function() {}, "jsonp").always(function(resp) {
          	    	   var countryCode = (resp && resp.country) ? resp.country : "";
          	    	   callback(countryCode);
         		    });
           		};
    	    }

    	    console.log(data);

    	    var mobileInput = '#'+this.uid;
    	    $(mobileInput).intlTelInput(data).done(function() {
    	    	self._initMask();
            	self._updateMobileNumber();
         	   $(mobileInput).on('keyup', function() {
         		  self._updateMobileNumber();
           	   }).on("countrychange", function(e, countryData) {
           		   self._initMask();
           		   self._updateMobileNumber();
          	   });
          	});
    	},
    	_initMask: function(){
    		var countryData = $('#'+this.uid).intlTelInput("getSelectedCountryData");
    		if(!countryData.iso2) return false;
    		var numberType = intlTelInputUtils.numberType['MOBILE'];
    		var mask = intlTelInputUtils.getExampleNumber(countryData.iso2, true, numberType);
    		$('#'+this.uid).mask(mask.replace(/([0-9])/g, '0'));
    	},
    	/**
    	 * Validate
    	 */
    	validate: function(){
    		var result = this._super();
    		if(!this.requireVerifying) return result;
    		
            if(!$('#'+this.uid).intlTelInput("isValidNumber")){
                result.valid = false;
                this.error($t('Your mobile number is not valid.'));
                this.showOtpForm(false);
                this.source.set('params.invalid', true);
                return result;
            }
    		return result;
    	},
    	
    	/**
    	 * Send OTP message
    	 */
    	sendOtp: function(isResend){
    	    if(this.isSending()) return;
    		var self = this;
    		this.isSending(true);
    		$.ajax({
        		  url: this.sendOtpUrl,
        		  method: "POST",
        		  data: {
            		  mobile : this.value(),
            		  resend : isResend,
      		  },
        		  dataType: "json"
      		}).done(function( response ){
                self.isSending(false);
      			if(response.success){
      				self.isSentOtp(true);
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
         * Resend Otp
         */
        resendOtp: function(){
        	if(this.countNum() != false) return;
        	this.sendOtp(true);
        },
        
        /**
         * Verify Otp
         */
    	verifyOtp: function(){
    	    if(this.isVerifying()) return;
    	    this.isVerifying(true);
    		$.ajax({
	  		  url: this.verifyOtpUrl,
	  		  method: "POST",
	  		  data: {
	      		  mobile : this.value(),
	      		  otp: this.otp()
			  },
	  		  dataType: "json"
			}).done(function(response){
			    this.isVerifying(false);
				this.otp('');
				if(response.success){
					this.otpValidated(true);
		    		this.showOtpForm(false);
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
    	
    	/**
    	 * Reset OTP Validation
    	 */
    	resetOtpValidation: function(){
    		this.isSentOtp(false);
    		this.otpValidated(false);
    	},
    	
    	/**
    	 * Update mobile number
    	 */
    	_updateMobileNumber: function(){
    		this.value($('#'+this.uid).intlTelInput("getNumber"));
    	}
	});
});