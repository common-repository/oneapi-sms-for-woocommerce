<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 * @package    Oneapi
 * @subpackage Oneapi/admin/partials
 * @author     OneAPI <info@oneapi.ru>
  */
class AngellEYE_Oneapi_SMS_For_Woo_Admin_Display {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_settings_menu'));
    }

    /**
     * add_settings_menu helper function used for add menu for pluging setting
     * @since    1.0.0
     * @access   public
     */
    public static function add_settings_menu() {
        add_options_page('Oneapi SMS For Woo', 'Oneapi SMS For Woo', 'manage_options', 'oneapi-sms-for-woo-option', array(__CLASS__, 'oneapi_sms_for_woo_options'));
    }

    /**
     * oneapi_sms_for_woo_options helper will trigger hook and handle all the settings section
     * @since    1.0.0
     * @access   public
     */
    public static function oneapi_sms_for_woo_options() {
        do_action('oneapi_sms_for_woo_setting_save_field');
        do_action('oneapi_sms_for_woo_setting');
    }

}

AngellEYE_Oneapi_SMS_For_Woo_Admin_Display::init();
