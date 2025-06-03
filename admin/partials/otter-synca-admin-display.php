<?php
/**
 * Provide a admin area view for the plugin
 *
 * @since      1.0.0
 * @package    Otter_Synca
 * @subpackage Otter_Synca/admin/partials
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Get the last deploy info
$last_deploy = get_option('otter_synca_last_deploy', array(
    'status' => '',
    'message' => '',
    'timestamp' => ''
));
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="otter-synca-admin-content">
        <div class="otter-synca-admin-main">
            <form method="post" action="options.php">
                <?php
                settings_fields($this->plugin_name);
                do_settings_sections($this->plugin_name);
                ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="otter_synca_github_token"><?php _e('GitHub Token', 'otter-synca'); ?></label>
                        </th>
                        <td>
                            <input type="password" 
                                   id="otter_synca_github_token" 
                                   name="otter_synca_github_token" 
                                   value="<?php echo esc_attr(get_option('otter_synca_github_token')); ?>" 
                                   class="regular-text">
                            <p class="description">
                                <?php _e('Your GitHub personal access token. Create one at: https://github.com/settings/tokens', 'otter-synca'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="otter_synca_repository"><?php _e('Repository', 'otter-synca'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="otter_synca_repository" 
                                   name="otter_synca_repository" 
                                   value="<?php echo esc_attr(get_option('otter_synca_repository')); ?>" 
                                   class="regular-text"
                                   placeholder="username/repository">
                            <p class="description">
                                <?php _e('The GitHub repository in the format: username/repository', 'otter-synca'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="otter_synca_branch"><?php _e('Branch', 'otter-synca'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="otter_synca_branch" 
                                   name="otter_synca_branch" 
                                   value="<?php echo esc_attr(get_option('otter_synca_branch', 'main')); ?>" 
                                   class="regular-text">
                            <p class="description">
                                <?php _e('The branch to deploy (default: main)', 'otter-synca'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="otter_synca_deploy_type"><?php _e('Deploy Type', 'otter-synca'); ?></label>
                        </th>
                        <td>
                            <select id="otter_synca_deploy_type" name="otter_synca_deploy_type">
                                <option value="plugin" <?php selected(get_option('otter_synca_deploy_type'), 'plugin'); ?>>
                                    <?php _e('Plugin', 'otter-synca'); ?>
                                </option>
                                <option value="theme" <?php selected(get_option('otter_synca_deploy_type'), 'theme'); ?>>
                                    <?php _e('Theme', 'otter-synca'); ?>
                                </option>
                            </select>
                            <p class="description">
                                <?php _e('Whether to deploy as a plugin or theme', 'otter-synca'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="otter_synca_target_slug"><?php _e('Target Slug', 'otter-synca'); ?></label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="otter_synca_target_slug" 
                                   name="otter_synca_target_slug" 
                                   value="<?php echo esc_attr(get_option('otter_synca_target_slug')); ?>" 
                                   class="regular-text">
                            <p class="description">
                                <?php _e('The slug of the plugin or theme to update', 'otter-synca'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Settings', 'otter-synca')); ?>
            </form>

            <div class="otter-synca-deploy-section">
                <h2><?php _e('Deploy Now', 'otter-synca'); ?></h2>
                <p><?php _e('Click the button below to deploy the latest version from your GitHub repository.', 'otter-synca'); ?></p>
                <button type="button" id="otter-synca-deploy" class="button button-primary">
                    <?php _e('Deploy Now', 'otter-synca'); ?>
                </button>
                <div id="otter-synca-deploy-status"></div>
            </div>
        </div>

        <div class="otter-synca-admin-sidebar">
            <div class="otter-synca-last-deploy">
                <h3><?php _e('Last Deploy', 'otter-synca'); ?></h3>
                <?php if (!empty($last_deploy['timestamp'])) : ?>
                    <p>
                        <strong><?php _e('Status:', 'otter-synca'); ?></strong>
                        <span class="otter-synca-status otter-synca-status-<?php echo esc_attr($last_deploy['status']); ?>">
                            <?php echo esc_html(ucfirst($last_deploy['status'])); ?>
                        </span>
                    </p>
                    <p>
                        <strong><?php _e('Message:', 'otter-synca'); ?></strong>
                        <?php echo esc_html($last_deploy['message']); ?>
                    </p>
                    <p>
                        <strong><?php _e('Time:', 'otter-synca'); ?></strong>
                        <?php echo esc_html($last_deploy['timestamp']); ?>
                    </p>
                <?php else : ?>
                    <p><?php _e('No deployments yet.', 'otter-synca'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 