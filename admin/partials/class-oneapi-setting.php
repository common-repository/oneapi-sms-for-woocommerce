<?php

/**
 * @class      Oneapi_SMS_For_Woo_Setting
 * @since      1.0.0
 * @package    Oneapi
 * @subpackage Oneapi/admin
 * @author     OneAPI <info@oneapi.ru>
 */
class Oneapi_SMS_For_Woo_Setting {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {
        add_action('oneapi_sms_for_woo_setting', array(__CLASS__, 'oneapi_sms_for_woo_setting_field'));
        add_action('oneapi_sms_for_woo_setting_save_field', array(__CLASS__, 'oneapi_sms_for_woo_setting_save_field'));
        add_action('wp_ajax_oneapi_sms_send_test_sms', array(__CLASS__, 'oneapi_sms_send_test_sms'));
    }

     public static function oneapi_sms_send_test_sms() {
        check_ajax_referer('oneapi_sms_woo_test_sms', 'security');
        $log = new Oneapi_Logger();
        require plugin_dir_path(dirname(__FILE__)) . 'partials/lib/Oneapi.php';
        $AppKey = get_option("oneapi_sms_woo_app_key");
        $AppSecret = get_option("oneapi_sms_woo_app_secret");
        $from_number = get_option("oneapi_sms_woo_from_number");
        $response = '';
        
        $test_mobile_number = $_POST['oneapi_sms_woo_test_phone'];
        $Test_Mobile_Array = '';               
        $Test_Mobile_Array = explode(',', $test_mobile_number);          
      
        $test_message = sanitize_text_field($_POST['oneapi_sms_woo_test_message']);

        
        if(is_array($Test_Mobile_Array) && count($Test_Mobile_Array) > 0){
            
            foreach ($Test_Mobile_Array as $key => $value) {
                try {
                    $client = new Services_Oneapi();
                    $message = $client->Oneapi_send_sms($AppKey,$AppSecret,$from_number,$value,$test_message);
                    $response['success'] = "Successfully Sent message {$message->sid}";
                    if ('yes' == get_option('oneapi_sms_woo_log_errors')) {
                        $log->add('Oneapi SMS', 'TEST SMS Sent message ' . $message->sid);
										
					$log->add('Oneapi SMS', '3'.$AppKey);
					$log->add('Oneapi SMS', '3'.$AppSecret);
					$log->add('Oneapi SMS', '3'.$from_number);
					$log->add('Oneapi SMS', '3'.$value);
					$log->add('Oneapi SMS', '3'.$test_message);	
						
						
                    }
                } catch (Exception $e) {
                    $response['error'] = $e->getMessage();
                    if ('yes' == get_option('oneapi_sms_woo_log_errors')) {
                        $log->add('Oneapi SMS', 'TEST SMS Error message ' . $e->getMessage());
                    }
                }                
            }            
        }
        echo json_encode($response);
        die();
    }

    public static function oneapi_sms_for_woo_general_setting_save_field() {
        $fields[] = array('title' => __('Настройки рассылки для менеджера магазина', 'oneapi-sms-for-woo'), 'type' => 'title', 'desc' => '', 'id' => 'admin_notifications_options');
        $fields[] = array(
            'title' => __('Активировать отправку СМС для менеджера магазина о покупке нового товара', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_enable_admin_sms',
            'default' => 'no',
            'type' => 'checkbox'
        );
        $fields[] = array(
            'title' => __('Номер телефона менеджера', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_admin_sms_recipients',
            'desc' => __('Введите номер мобильного телефона менеджера магазина (например: 79261234567).', 'oneapi-sms-for-woo'),
            'default' => '74957409085',
            'type' => 'text'
        );
        $fields[] = array(
            'title' => __('Текст СМС для менеджера', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_admin_sms_template',
            'desc' => __('Используйте следующие параметры для редактирования сообщения: %shop_name%, %order_id%, %order_amount%.', 'oneapi-sms-for-woo'),
            'css' => 'min-width:500px;',
            'default' => __('%shop_name% : Новый заказ %order_id% на сумму %order_amount%!', 'oneapi-sms-for-woo'),
            'type' => 'textarea'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        $fields[] = array('title' => __('Настройки рассылки для покупателя', 'oneapi-sms-for-woo'), 'type' => 'title', 'desc' => '', 'id' => 'customer_notification_options');
        $fields[] = array(
            'title' => __('Отправлять СМС для следующих статусов', 'oneapi-sms-for-woo'),
            'desc' => __('Новый заказ', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_send_sms_pending',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => 'start'
        );
        $fields[] = array(
            'desc' => __('Заказ на удержании', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_send_sms_on-hold',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => '',
            'autoload' => false
        );
        $fields[] = array(
            'desc' => __('Заказ в обработке', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_send_sms_processing',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => '',
            'autoload' => false
        );
        $fields[] = array(
            'desc' => __('Выполненный заказ', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_send_sms_completed',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => '',
            'autoload' => false
        );
        $fields[] = array(
            'desc' => __('Отмененный заказ', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_send_sms_cancelled',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => '',
            'autoload' => false
        );
        $fields[] = array(
            'desc' => __('Возвращенный заказ', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_send_sms_refunded',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => '',
            'autoload' => false
        );
        $fields[] = array(
            'desc' => __('Неудачный заказ', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_send_sms_failed',
            'default' => 'yes',
            'type' => 'checkbox',
            'checkboxgroup' => 'end',
            'autoload' => false
        );
        $fields[] = array(
            'title' => __('Сообщение по-умолчанию для покупателя', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_default_sms_template',
            'default' => __('%shop_name% : Ваш заказ (%order_id%) имеет статус %order_status%.', 'oneapi-sms-for-woo'),
            'type' => 'textarea',
            'css' => 'min-width:500px;'
        );
        $fields[] = array(
            'title' => __('Сообщение для нового заказа', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_pending_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('Сообщение для заказа на удержании', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_on-hold_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('Сообщение для заказа в обработке', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_processing_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('Сообщение для выполненого заказа', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_completed_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('Сообщение для отмененного заказа', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_cancelled_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('Сообщение для возвращенного заказа', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_refunded_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array(
            'title' => __('Сообщение для неудачного заказа', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_failed_sms_template',
            'css' => 'min-width:500px;',
            'type' => 'textarea'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        $fields[] = array('title' => __('Настройки Oneapi', 'oneapi-sms-for-woo'), 'type' => 'title', 'desc' => '', 'id' => 'oneapi_settings_options');
        $fields[] = array(
            'title' => __('App Key', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_app_key',
            'desc' => __('Войдите в личный кабинет OneAPI.ru, чтобы получить значение App Key', 'oneapi-sms-for-woo'),
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('App Secret', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_app_secret',
            'desc' => __('Войдите в личный кабинет OneAPI.ru, чтобы получить значение App Secret', 'oneapi-sms-for-woo'),
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'title' => __('С какого номера', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_from_number',
            'desc' => __('Введите номер, с которого будут приходить сообщения. Данный номер должен быть куплен в OneAPI.ru.', 'oneapi-sms-for-woo'),
            'type' => 'text',
            'css' => 'min-width:300px;',
        );
        $fields[] = array(
            'desc' => __('Включить логирование.', 'oneapi-sms-for-woo'),
            'title' => __('Log Errors', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_log_errors',
            'default' => 'no',
            'type' => 'checkbox'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        $fields[] = array('title' => __('Отправить тестовую СМС', 'oneapi-sms-for-woo'), 'type' => 'title', 'desc' => '', 'id' => 'send_test_sms_options');
        $fields[] = array(
            'title' => __('Mobile Number', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_test_mobile_number',
            'name' => 'oneapi_sms_woo_test_mobile_number',
            'desc' => __('Введите номер мобильного телефона для тестового сообщения (например: 79261234567).', 'oneapi-sms-for-woo'),
            'type' => 'text',
            'css' => 'min-width:300px;'
        );
        $fields[] = array(
            'title' => __('Тестовое сообщений', 'oneapi-sms-for-woo'),
            'id' => 'oneapi_sms_woo_test_message',
            'name' => 'oneapi_sms_woo_test_message',
            'desc' => __('Текст тестового сообщения.', 'oneapi-sms-for-woo'),
            'type' => 'textarea',
            'css' => 'min-width: 500px;'
        );
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function oneapi_sms_for_woo_setting_field() {
        $sms_setting_fields = self::oneapi_sms_for_woo_general_setting_save_field();
        $Html_output = new Oneapi_SMS_For_Woo_Html_output();
        ?>
        <form id="oneapi_sms_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($sms_setting_fields); ?>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" class="titledesc"></th>
                        <td class="forminp">
                            <input type="button" class="button" id="oneapi_sms_test_sms_button" name="oneapi_sms_test_sms_button" value="<?php esc_attr_e('Send', 'Option'); ?>"/>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit">
                <input type="submit" name="oneapi_sms" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function oneapi_sms_for_woo_setting_save_field() {
        $oneapi_sms_setting_fields = self::oneapi_sms_for_woo_general_setting_save_field();
        $Html_output = new Oneapi_SMS_For_Woo_Html_output();
        $Html_output->save_fields($oneapi_sms_setting_fields);
    }

}

Oneapi_SMS_For_Woo_Setting::init();
