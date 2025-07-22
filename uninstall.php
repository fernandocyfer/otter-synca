<?php

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('otter_synca_github_token');
delete_option('otter_synca_repository');
delete_option('otter_synca_branch');
delete_option('otter_synca_deploy_type');
delete_option('otter_synca_target_slug');
delete_option('otter_synca_last_deploy'); 