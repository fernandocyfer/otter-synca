<?php
/**
 * Provide a admin area view for the plugin settings
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

$last_deploy = get_option('otter_synca_last_deploy', array());
?>

<div class="otter-synca-card">
    <div class="otter-synca-card-header">
        <h2 class="otter-synca-card-title"><?php esc_html_e('Plugin Settings', 'otter-synca'); ?></h2>
    </div>

    <form method="post" action="options.php">
        <?php
        settings_fields('otter_synca_options');
        do_settings_sections('otter_synca_options');
        ?>

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="otter_synca_github_token"><?php esc_html_e('GitHub Token', 'otter-synca'); ?></label>
                </th>
                <td>
                    <input type="password" 
                           id="otter_synca_github_token" 
                           name="otter_synca_github_token" 
                           value="<?php echo esc_attr(get_option('otter_synca_github_token')); ?>" 
                           class="regular-text" />
                    <p class="description">
                        <?php esc_html_e('GitHub personal access token with repository permissions.', 'otter-synca'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="otter_synca_repository"><?php esc_html_e('Repository', 'otter-synca'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           id="otter_synca_repository" 
                           name="otter_synca_repository" 
                           value="<?php echo esc_attr(get_option('otter_synca_repository')); ?>" 
                           class="regular-text" 
                           placeholder="username/repository" />
                    <p class="description">
                        <?php esc_html_e('Repository name in the format username/repository.', 'otter-synca'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="otter_synca_branch"><?php esc_html_e('Branch', 'otter-synca'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           id="otter_synca_branch" 
                           name="otter_synca_branch" 
                           value="<?php echo esc_attr(get_option('otter_synca_branch', 'main')); ?>" 
                           class="regular-text" />
                    <p class="description">
                        <?php esc_html_e('Repository branch to deploy.', 'otter-synca'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="otter_synca_deploy_type"><?php esc_html_e('Deploy Type', 'otter-synca'); ?></label>
                </th>
                <td>
                    <select id="otter_synca_deploy_type" name="otter_synca_deploy_type">
                        <option value="plugin" <?php selected(get_option('otter_synca_deploy_type'), 'plugin'); ?>>
                            <?php esc_html_e('Plugin', 'otter-synca'); ?>
                        </option>
                        <option value="theme" <?php selected(get_option('otter_synca_deploy_type'), 'theme'); ?>>
                            <?php esc_html_e('Theme', 'otter-synca'); ?>
                        </option>
                    </select>
                    <p class="description">
                        <?php esc_html_e('Type of deployment to perform.', 'otter-synca'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="otter_synca_target_slug"><?php esc_html_e('Target Slug', 'otter-synca'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           id="otter_synca_target_slug" 
                           name="otter_synca_target_slug" 
                           value="<?php echo esc_attr(get_option('otter_synca_target_slug')); ?>" 
                           class="regular-text" />
                    <p class="description">
                        <?php esc_html_e('Target plugin or theme slug.', 'otter-synca'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="otter_synca_auto_deploy"><?php esc_html_e('Auto Deploy', 'otter-synca'); ?></label>
                </th>
                <td>
                    <label>
                        <input type="checkbox" 
                               id="otter_synca_auto_deploy" 
                               name="otter_synca_auto_deploy" 
                               value="1" 
                               disabled />
                        <?php esc_html_e('Enable automatic deployment via webhook', 'otter-synca'); ?>
                    </label>
                    
                    <?php if (!class_exists('Otter_Synca_Pro')): ?>
                        <!-- Mensagem para usuários sem o Pro -->
                        <div style="margin-top: 8px; padding: 10px; background: #fffbe5; border: 1px solid #ffe066; border-radius: 4px; color: #b38f00; max-width: 400px;">
                            <strong><?php esc_html_e('Exclusive Pro feature!', 'otter-synca'); ?></strong><br>
                            <?php esc_html_e('Auto deploy is available only in OtterSynca Pro.', 'otter-synca'); ?>
                            <a href="https://plugins.cyfer.com.br/downloads/ottersynca-deploy-para-wordpress/" target="_blank" class="button button-primary" style="margin-top: 8px; margin-left: 0; display: inline-block;">
                                <?php esc_html_e('Learn about OtterSynca Pro', 'otter-synca'); ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- Mensagem para usuários com o Pro ativo -->
                        <div style="margin-top: 8px; padding: 10px; background: #e5f3ff; border: 1px solid #66b3ff; border-radius: 4px; color: #0066cc; max-width: 400px;">
                            <strong><?php esc_html_e('OtterSynca Pro active!', 'otter-synca'); ?></strong><br>
                            <?php esc_html_e('Configure auto deploy on the OtterSynca Pro page.', 'otter-synca'); ?>
                            <a href="<?php echo admin_url('admin.php?page=otter-synca-pro'); ?>" class="button button-primary" style="margin-top: 8px; margin-left: 0; display: inline-block;">
                                <?php esc_html_e('Configure in Pro', 'otter-synca'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <p class="description">
                        <?php esc_html_e('When enabled, the plugin will automatically deploy when there are commits to the configured branch.', 'otter-synca'); ?>
                    </p>
                </td>
            </tr>

            <!-- Campos ocultos na versão free - relacionados ao deploy automático -->
            <tr style="display: none;">
                <th scope="row">
                    <label for="otter_synca_webhook_secret"><?php esc_html_e('Webhook Secret', 'otter-synca'); ?></label>
                </th>
                <td>
                    <div class="otter-synca-webhook-secret-container">
                        <input type="password" 
                               id="otter_synca_webhook_secret" 
                               name="otter_synca_webhook_secret" 
                               value="<?php echo esc_attr(get_option('otter_synca_webhook_secret')); ?>" 
                               class="regular-text" 
                               placeholder="<?php esc_attr_e('Click "Generate Secret" or type manually', 'otter-synca'); ?>" />
                        <button type="button" class="button button-secondary" id="generate-webhook-secret">
                            <?php esc_html_e('Generate Secret', 'otter-synca'); ?>
                        </button>
                    </div>
                    <p class="description">
                        <?php esc_html_e('GitHub webhook secret. Use the "Generate Secret" button to create a secure key automatically.', 'otter-synca'); ?>
                    </p>
                </td>
            </tr>

            <tr style="display: none;">
                <th scope="row">
                    <label><?php esc_html_e('Webhook URL', 'otter-synca'); ?></label>
                </th>
                <td>
                    <input type="text" 
                           value="<?php echo esc_attr(Otter_Synca_Webhook::get_webhook_url()); ?>" 
                           class="regular-text" 
                           readonly 
                           onclick="this.select();" />
                    <p class="description">
                        <?php esc_html_e('Configure this URL in your GitHub repository as a webhook to enable automatic deployment.', 'otter-synca'); ?>
                    </p>
                    <p class="description">
                        <strong><?php esc_html_e('How to configure:', 'otter-synca'); ?></strong><br>
                        1. <?php esc_html_e('Go to Settings > Webhooks in your GitHub repository', 'otter-synca'); ?><br>
                        2. <?php esc_html_e('Click "Add webhook"', 'otter-synca'); ?><br>
                        3. <?php esc_html_e('Paste the URL above in the "Payload URL" field', 'otter-synca'); ?><br>
                        4. <?php esc_html_e('Select "Just the push event"', 'otter-synca'); ?><br>
                        5. <?php esc_html_e('Add the secret above (optional)', 'otter-synca'); ?><br>
                        6. <?php esc_html_e('Click "Add webhook"', 'otter-synca'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(esc_html__('Save Settings', 'otter-synca')); ?>
    </form>
</div> 