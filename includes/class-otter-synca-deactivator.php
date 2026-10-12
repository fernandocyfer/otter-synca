<?php

/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    Otter_Synca
 * @subpackage Otter_Synca/includes
 */
class Otter_Synca_Deactivator {

    /**
     * Clean up plugin data on deactivation.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        delete_option('otter_synca_deploy_lock');
        flush_rewrite_rules(false);
    }
} 