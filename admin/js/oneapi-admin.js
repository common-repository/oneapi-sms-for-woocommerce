jQuery(function ($) {
    $("#oneapi_sms_test_sms_button").live("click", function () {
        var oneapi_sms_woo_test_phone = $("#oneapi_sms_woo_test_mobile_number").val();
        var oneapi_sms_woo_test_message = $("#oneapi_sms_woo_test_message").val();
        var data = {
            action: 'oneapi_sms_send_test_sms',
            security: oneapi_sms_test_sms_button_params.oneapi_sms_woo_test_sms,
            oneapi_sms_woo_test_phone: oneapi_sms_woo_test_phone,
            oneapi_sms_woo_test_message: oneapi_sms_woo_test_message
        };
        $.post(oneapi_sms_test_sms_button_params.ajax_url, data, function (response) {
            response = JSON.parse(response);
            if (typeof (response.success) !== 'undefined') {
                if (response.success.length > 0) {
                    alert(response.success);
                } else {
                    alert(response.error);
                }
            } else {
                alert(response.error);
            }
        });
    });
});