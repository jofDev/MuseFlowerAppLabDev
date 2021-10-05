var config = {
	"map": {
        '*': {
            "smsLogin": 'Vnecoms_Sms/js/login',
        }
    },
    "shim": {
        "jquery/intltellinput": ["jquery"],
        "jquery/mask": ["jquery"],
        "Vnecoms_Sms/js/utils": ["jquery", "jquery/intltellinput"]
    },
    "paths": {
        "jquery/intltellinput": "Vnecoms_Sms/js/intlTelInput",
        "jquery/mask": "Vnecoms_Sms/js/jquery-mask"
    }
};
