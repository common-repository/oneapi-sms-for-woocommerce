<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Oneapi
 * @subpackage Oneapi/includes
 * @author     OneAPI <info@oneapi.ru>
 */
class Oneapi_Activator {

    /**
     * @since    1.0.0
     */
    public static function activate() {
        self::create_files();
    }

   
    private function create_files() {
        $files = array(
            array(
                'base' => ONEAPI_LOG_DIR,
                'file' => '.htaccess',
                'content' => 'deny from all'
            ),
            array(
                'base' => ONEAPI_LOG_DIR,
                'file' => 'index.html',
                'content' => ''
            )
        );
        foreach ($files as $file) {
            if (wp_mkdir_p($file['base']) && !file_exists(trailingslashit($file['base']) . $file['file'])) {
                if ($file_handle = @fopen(trailingslashit($file['base']) . $file['file'], 'w')) {
                    fwrite($file_handle, $file['content']);
                    fclose($file_handle);
                }
            }
        }
    }

}
