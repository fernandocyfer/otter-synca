<?php

/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    Otter_Synca
 * @subpackage Otter_Synca/includes
 */
class Otter_Synca_Activator {

    /**
     * Create necessary database tables and options.
     *
     * @since    1.0.0
     */
    public static function activate() {
        // Add default options
        add_option('otter_synca_github_token', '');
        add_option('otter_synca_repository', '');
        add_option('otter_synca_branch', 'main');
        add_option('otter_synca_deploy_type', 'plugin');
        add_option('otter_synca_target_slug', '');
        add_option('otter_synca_last_deploy', array(
            'status' => '',
            'message' => '',
            'timestamp' => '',
        ));
    }
} 