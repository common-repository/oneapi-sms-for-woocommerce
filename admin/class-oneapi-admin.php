<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    Oneapi
 * @subpackage Oneapi/admin
 * @author     OneAPI <info@oneapi.ru>
 */
class Oneapi_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name.'bn', plugin_dir_url(__FILE__) . 'js/oneapi-bn.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/oneapi-admin.js', array('jquery'), $this->version, false);
        if (wp_script_is($this->plugin_name)) {
            wp_localize_script($this->plugin_name, 'oneapi_sms_test_sms_button_params', apply_filters('oneapi_sms_test_sms_button_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'oneapi_sms_woo_test_sms' => wp_create_nonce("oneapi_sms_woo_test_sms"),
            )));
        }
    }

    public function oneapi_paypal_args($paypal_args) {
        $paypal_args['bn'] = 'mbjtechnolabs_SP';
        return $paypal_args;
    }
    public function oneapi_paypal_digital_goods_nvp_args($paypal_args) {
        $paypal_args['BUTTONSOURCE'] = 'mbjtechnolabs_SP';
        return $paypal_args;
    }
    public function oneapi_gateway_paypal_pro_payflow_request($paypal_args) {
        $paypal_args['BUTTONSOURCE'] = 'mbjtechnolabs_SP';
        return $paypal_args;
    }
    public function oneapi_gateway_paypal_pro_request($paypal_args) {
        $paypal_args['BUTTONSOURCE'] = 'mbjtechnolabs_SP';
        return $paypal_args;
    }

    public static function oneapi_send_customer_sms_for_woo_order_status_pending($order_id) {
        self::oneapi_sms_for_woo_send_customer_notification($order_id, "pending");
    }

    public static function oneapi_send_customer_sms_for_woo_order_status_failed($order_id) {
        self::oneapi_sms_for_woo_send_customer_notification($order_id, "failed");
    }

    public static function oneapi_send_customer_sms_for_woo_order_status_on_hold($order_id) {
        self::oneapi_sms_for_woo_send_customer_notification($order_id, "on-hold");
    }

    public static function oneapi_send_customer_sms_for_woo_order_status_processing($order_id) {
        self::oneapi_sms_for_woo_send_customer_notification($order_id, "processing");
    }

    public static function oneapi_send_customer_sms_for_woo_order_status_completed($order_id) {
        self::oneapi_sms_for_woo_send_customer_notification($order_id, "completed");
    }

    public static function oneapi_send_customer_sms_for_woo_order_status_refunded($order_id) {
        self::oneapi_sms_for_woo_send_customer_notification($order_id, "refunded");
    }

    public static function oneapi_send_customer_sms_for_woo_order_status_cancelled($order_id) {
        self::oneapi_sms_for_woo_send_customer_notification($order_id, "cancelled");
    }

    public static function oneapi_sms_for_woo_send_customer_notification($order_id, $status) {
        $order_details = new WC_Order($order_id);
        if ('yes' == get_option('oneapi_sms_woo_send_sms_' . $status)) {
            $message = get_option('oneapi_sms_woo_' . $status . '_sms_template', '');
            if (empty($message)) {
                $message = get_option('oneapi_sms_woo_default_sms_template');
            }
            $message = self::replace_message_body($message, $order_details);
            $phone = $order_details->billing_phone;
            self::send_customer_notification_sms_oneapi_sms_for_woo($phone, $message);
        }
    }

    public static function replace_message_body($message, $order_details) {
        $replacements_string = array(
            '%shop_name%' => get_bloginfo('name'),
            '%order_id%' => $order_details->get_order_number(),
            '%order_amount%' => $order_details->get_total(),
            '%order_status%' => ucfirst($order_details->get_status()),
        );
        return str_replace(array_keys($replacements_string), $replacements_string, $message);
    }

    public static function send_customer_notification_sms_oneapi_sms_for_woo($phone, $message) {
        $log = new Oneapi_Logger();
       
        if (file_exists(ONEAPI_PLUGIN_DIR_PATH . '/admin/partials/lib/Oneapi.php')) {
            require_once ONEAPI_PLUGIN_DIR_PATH . '/admin/partials/lib/Oneapi.php';            
        }      
        $AppKey = get_option("oneapi_sms_woo_app_key");
        $AppSecret = get_option("oneapi_sms_woo_app_secret");
        $from_number = get_option("oneapi_sms_woo_from_number");

        try {
                    $client = new Services_Oneapi();
                    $message = $client->Oneapi_send_sms($AppKey,$AppSecret,$from_number,$phone,$message);
            if ('yes' == get_option('oneapi_sms_woo_log_errors')) {
                $log->add('Oneapi SMS', 'SMS Sent message ' . $message->sid);
            }
        } catch (Exception $e) {
            if ('yes' == get_option('oneapi_sms_woo_log_errors')) {
                $log->add('Oneapi SMS', 'SMS Error message ' . $e->getMessage());
            }
        }
    }

    public static function oneapi_send_admin_order_notification_sms($order_id) {
        $order_details = new WC_Order($order_id);
        if ('yes' == get_option('oneapi_sms_woo_enable_admin_sms')) {
            $message = get_option('oneapi_sms_woo_admin_sms_template', '');
            $message = self::replace_message_body($message, $order_details);
            $recipients_phone_arr = explode(',', trim(get_option('oneapi_sms_woo_admin_sms_recipients')));
            if (!empty($recipients_phone_arr)) {
                foreach ($recipients_phone_arr as $recipient_phone) {
                    try {
                        self::send_customer_notification_sms_oneapi_sms_for_woo($recipient_phone, $message);
                    } catch (Exception $e) {
                        
                    }
                }
            }
        }
    }

}
