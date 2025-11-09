<?php
/**
 * Provide a admin area view for the plugin help
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
?>

<div class="otter-synca-card">
    <div class="otter-synca-card-header">
        <h2 class="otter-synca-card-title"><?php esc_html_e('Getting Started', 'otter-synca'); ?></h2>
    </div>

    <div class="otter-synca-help-steps">
        <div class="otter-synca-help-step">
            <h3><?php esc_html_e('1. Create a GitHub Token', 'otter-synca'); ?></h3>
            <p><?php esc_html_e('To get started, you need to create a personal access token on GitHub:', 'otter-synca'); ?></p>
            <ol>
                <li><?php esc_html_e('Go to GitHub settings', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Navigate to "Developer settings" > "Personal access tokens" > "Tokens (classic)"', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Click "Generate new token"', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Give the token a name and select "repo" permissions', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Copy the generated token and paste it in the "GitHub Token" field in the plugin settings', 'otter-synca'); ?></li>
            </ol>
        </div>

        <div class="otter-synca-help-step">
            <h3><?php esc_html_e('2. Configure the Plugin', 'otter-synca'); ?></h3>
            <p><?php esc_html_e('After creating the token, configure the plugin with the following information:', 'otter-synca'); ?></p>
            <ul>
                <li><?php esc_html_e('Repository: Repository name in the format username/repository', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Branch: Branch you want to deploy (default: main)', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Deploy Type: Select whether it is a plugin or theme', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Target Slug: Name of the plugin or theme that will be updated', 'otter-synca'); ?></li>
            </ul>
        </div>

        <div class="otter-synca-help-step">
            <h3><?php esc_html_e('3. Deploy', 'otter-synca'); ?></h3>
            <p><?php esc_html_e('With everything configured, you can deploy:', 'otter-synca'); ?></p>
            <ol>
                <li><?php esc_html_e('Verify that all settings are correct', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Click the "Deploy Now" button', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Wait for the process to complete', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Check the deploy status in the sidebar', 'otter-synca'); ?></li>
            </ol>
        </div>
    </div>
</div>

<div class="otter-synca-card">
    <div class="otter-synca-card-header">
        <h2 class="otter-synca-card-title"><?php esc_html_e('Frequently Asked Questions', 'otter-synca'); ?></h2>
    </div>

    <div class="otter-synca-faq">
        <h3><?php esc_html_e('What permissions does the GitHub token need?', 'otter-synca'); ?></h3>
        <p><?php esc_html_e('The token needs repository access permission (repo). This allows the plugin to download code from the repository.', 'otter-synca'); ?></p>

        <h3><?php esc_html_e('Can I deploy multiple repositories?', 'otter-synca'); ?></h3>
        <p><?php esc_html_e('Yes! In the premium version of the plugin you can configure multiple repositories and manage them centrally.', 'otter-synca'); ?></p>

        <h3><?php esc_html_e('Does the plugin backup before deploying?', 'otter-synca'); ?></h3>
        <p><?php esc_html_e('In the premium version, the plugin automatically backs up before each deployment, allowing you to revert to the previous version if necessary.', 'otter-synca'); ?></p>
    </div>
</div>

<div class="otter-synca-card">
    <div class="otter-synca-card-header">
        <h2 class="otter-synca-card-title"><?php esc_html_e('Need more help?', 'otter-synca'); ?></h2>
    </div>

    <p><?php esc_html_e('If you didn\'t find the answer you were looking for, contact us:', 'otter-synca'); ?></p>
    <a href="https://github.com/fernandocyfer/OtterSynca/issues" class="button button-primary" target="_blank">
        <?php esc_html_e('Open an Issue on GitHub', 'otter-synca'); ?>
    </a>
</div>

<style>
.otter-synca-card {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.otter-synca-card-header {
    margin-bottom: 20px;
}

.otter-synca-card-title {
    margin-top: 0;
    color: #2271b1;
}

.otter-synca-help-steps {
    display: grid;
    gap: 20px;
    margin: 20px 0;
}

.otter-synca-help-step {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.otter-synca-help-step h3 {
    margin-top: 0;
    color: #2271b1;
}

.otter-synca-faq {
    display: grid;
    gap: 20px;
}

.otter-synca-faq-item {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.otter-synca-faq-item h3 {
    margin-top: 0;
    color: #2271b1;
}
</style> 