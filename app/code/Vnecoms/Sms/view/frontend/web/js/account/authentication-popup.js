define([
    'jquery',
    'Magento_Customer/js/view/authentication-popup',
	'smsLogin'
], function ($, Element) {
	return Element.extend({
		defaults:{
            initialCountry: '',
			allowDropdown: '',
			onlyCountries: '',
			preferredCountries: '',
			geoIpUrl: '',
			mobilenum: '',
			loginByMobile: false
		},
		/**
		 * Initializes observable properties of instance
		 *
		 * @returns {Abstract} Chainable.
		 */
		initObservable: function () {
			this._super();
			this.observe('loginByMobile mobilenum');

			return this;
		},

		/**
		 * Toggle login by mobile or email
		 */
		toggleLoginField: function(){
			this.loginByMobile(!this.loginByMobile());
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

			var mobileInput = '#login-mobile';
			$(mobileInput).intlTelInput(data).done(function() {
				self._updateMobileNumber();
				$(mobileInput).on('keyup', function() {
					self._updateMobileNumber();
				}).on("countrychange", function(e, countryData) {
					self._updateMobileNumber();
				});
			});
		},
		/**
		 * Update mobile number
		 */
		_updateMobileNumber: function(){
			this.mobilenum($('#login-mobile').intlTelInput("getNumber"));
		},
		test: function(){

		}
	});
});
