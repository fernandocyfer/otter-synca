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
     * Note: WordPress automatically loads translations since version 4.6.
     * This method is kept for backward compatibility but is no longer needed.
     *
     * @since    1.0.1
     * @deprecated WordPress now automatically loads translations
     */
    public function load_plugin_textdomain() {
        // WordPress automatically loads translations for plugins hosted on WordPress.org
        // No manual loading needed since WordPress 4.6
    }
} 