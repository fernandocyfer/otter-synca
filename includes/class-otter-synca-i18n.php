<?php

/**
 * Define the internationalization functionality.
 *
 * @since      1.0.1
 * @package    Otter_Synca
 * @subpackage Otter_Synca/includes
 */
class Otter_Synca_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.1
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'otter-synca',
            false,
            dirname(plugin_basename(dirname(__DIR__) . '/otter-synca.php')) . '/languages/'
        );
    }
} 