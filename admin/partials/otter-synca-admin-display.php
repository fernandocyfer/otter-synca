<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/fernandocyfer/otter-synca
 * @since      1.0.1
 *
 * @package    Otter_Synca
 * @subpackage Otter_Synca/admin/partials
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Get current tab - Use filter_input() instead of direct $_GET access (WordPress recommended practice).
$current_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ?: 'settings';
?>

<div class="wrap otter-synca-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
     <h3>Deploys from GitHub to WordPress. <span><i>Just like that.</i></span></h3>

    <nav class="nav-tab-wrapper">
        <a href="?page=otter-synca&tab=settings" class="nav-tab <?php echo $current_tab === 'settings' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Settings', 'otter-synca'); ?>
        </a>
        <a href="?page=otter-synca&tab=premium" class="nav-tab <?php echo $current_tab === 'premium' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Premium', 'otter-synca'); ?>
        </a>
        <a href="?page=otter-synca&tab=help" class="nav-tab <?php echo $current_tab === 'help' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Help', 'otter-synca'); ?>
        </a>
        <a href="?page=otter-synca&tab=coffee" class="nav-tab <?php echo $current_tab === 'coffee' ? 'nav-tab-active' : ''; ?>">
            <?php esc_html_e('Buy Me a Coffee', 'otter-synca'); ?>
        </a>
    </nav>

    <div class="otter-synca-admin-content">
        <div class="otter-synca-admin-main">
                <?php
            switch ($current_tab) {
                case 'settings':
                    include plugin_dir_path(__FILE__) . 'otter-synca-admin-settings.php';
                    break;
                case 'premium':
                    include plugin_dir_path(__FILE__) . 'otter-synca-admin-premium.php';
                    break;
                case 'help':
                    include plugin_dir_path(__FILE__) . 'otter-synca-admin-help.php';
                    break;
                case 'coffee':
                    include plugin_dir_path(__FILE__) . 'otter-synca-admin-coffee.php';
                    break;
                default:
                    include plugin_dir_path(__FILE__) . 'otter-synca-admin-settings.php';
            }
            ?>
        </div>

        <?php if ($current_tab === 'settings'): ?>
        <div class="otter-synca-admin-sidebar">
            <div class="otter-synca-card">
                <div class="otter-synca-card-header">
                    <h2 class="otter-synca-card-title"><?php esc_html_e('Deploy', 'otter-synca'); ?></h2>
                </div>

                <p><?php esc_html_e('Click the button below to deploy the latest version of the repository.', 'otter-synca'); ?></p>

                <button type="button" id="otter-synca-deploy" class="button button-primary">
                    <?php esc_html_e('Deploy Now', 'otter-synca'); ?>
                </button>

                <div id="otter-synca-deploy-status"></div>
            </div>

            <div class="otter-synca-card">
                <div class="otter-synca-card-header">
                    <h2 class="otter-synca-card-title"><?php esc_html_e('Last Deploy', 'otter-synca'); ?></h2>
                </div>
                <?php
                $last_deploy = get_option('otter_synca_last_deploy');
                if ($last_deploy) {
                    $status_class = $last_deploy['status'] === 'success' ? 'otter-synca-status-success' : 'otter-synca-status-error';
                    ?>
                    <div class="otter-synca-status <?php echo esc_attr($status_class); ?>">
                        <?php echo esc_html($last_deploy['status'] === 'success' ? __('Success', 'otter-synca') : __('Error', 'otter-synca')); ?>
                    </div>
                    <p><?php echo esc_html($last_deploy['message']); ?></p>
                    
                    <?php if (isset($last_deploy['commit_sha'])): ?>
                        <p class="description">
                            <strong><?php esc_html_e('Commit:', 'otter-synca'); ?></strong> 
                            <code><?php echo esc_html(substr($last_deploy['commit_sha'], 0, 8)); ?></code>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (isset($last_deploy['branch'])): ?>
                        <p class="description">
                            <strong><?php esc_html_e('Branch:', 'otter-synca'); ?></strong> 
                            <?php echo esc_html($last_deploy['branch']); ?>
                        </p>
                    <?php endif; ?>
                    
                    <p class="description">
                        <?php echo esc_html(sprintf(
                            /* translators: %s: date and time */
                            __('At: %s', 'otter-synca'),
                            date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($last_deploy['timestamp']))
                        )); ?>
                    </p>
                <?php } else { ?>
                    <p><?php esc_html_e('No deploy has been performed yet.', 'otter-synca'); ?></p>
                <?php } ?>
            </div>

            <?php if (get_option('otter_synca_auto_deploy')): ?>
                <?php if (class_exists('Otter_Synca_Pro')): ?>
                <div class="otter-synca-card">
                    <div class="otter-synca-card-header">
                        <h2 class="otter-synca-card-title"><?php esc_html_e('Auto Deploy', 'otter-synca'); ?></h2>
                    </div>
                    <div class="otter-synca-status otter-synca-status-success">
                        <?php esc_html_e('Enabled', 'otter-synca'); ?>
                    </div>
                    <p><?php esc_html_e('Auto deploy is active. Commits to the configured branch will trigger automatic deployment.', 'otter-synca'); ?></p>
                    
                    <?php
                    $last_deployed_commit = get_option('otter_synca_last_deployed_commit');
                    if ($last_deployed_commit): ?>
                        <p class="description">
                            <strong><?php esc_html_e('Last deployed commit:', 'otter-synca'); ?></strong><br>
                            <code><?php echo esc_html($last_deployed_commit); ?></code>
                        </p>
                    <?php endif; ?>
                    
                    <p class="description">
                        <strong><?php esc_html_e('Webhook URL:', 'otter-synca'); ?></strong><br>
                        <code><?php echo esc_html(Otter_Synca_Webhook::get_webhook_url()); ?></code>
                    </p>
                    
                    <button type="button" 
                            class="button button-secondary otter-synca-test-webhook" 
                            data-nonce="<?php echo wp_create_nonce('otter_synca_nonce'); ?>">
                        <?php esc_html_e('Test Webhook', 'otter-synca'); ?>
                    </button>
                    <span class="otter-synca-test-result" style="display: none; margin-left: 10px;"></span>
                </div>
                <?php else: ?>
                <div class="otter-synca-card otter-synca-pro-feature-locked">
                    <div class="otter-synca-card-header">
                        <h2 class="otter-synca-card-title">
                            <?php esc_html_e('Auto Deploy', 'otter-synca'); ?>
                            <span class="otter-synca-pro-badge"><?php esc_html_e('Pro', 'otter-synca'); ?></span>
                        </h2>
                    </div>
                    <p><?php esc_html_e('Auto deploy is a premium feature. Activate OtterSynca Pro to use automatic webhooks.', 'otter-synca'); ?></p>
                    <a href="<?php echo admin_url('admin.php?page=otter-synca&tab=premium'); ?>" class="button button-primary">
                        <?php esc_html_e('View Pro Features', 'otter-synca'); ?>
                    </a>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div> 