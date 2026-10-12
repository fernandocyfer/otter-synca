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

<div class="otter-synca-help-page">
    <section class="otter-synca-help-hero">
        <div>
            <span class="otter-synca-help-eyebrow"><?php esc_html_e('Help Center', 'otter-synca'); ?></span>
            <h2><?php esc_html_e('Getting Started', 'otter-synca'); ?></h2>
            <p><?php esc_html_e('Configure your repository and complete your first deployment in three steps.', 'otter-synca'); ?></p>
        </div>
        <span class="dashicons dashicons-update" aria-hidden="true"></span>
    </section>

    <section class="otter-synca-help-section" aria-labelledby="otter-synca-steps-title">
        <div class="otter-synca-help-section-heading">
            <div>
                <span class="otter-synca-help-eyebrow"><?php esc_html_e('Quick setup', 'otter-synca'); ?></span>
                <h2 id="otter-synca-steps-title"><?php esc_html_e('Three steps to deploy', 'otter-synca'); ?></h2>
            </div>
        </div>

        <div class="otter-synca-help-steps">
            <article class="otter-synca-help-step">
                <span class="otter-synca-help-step-number" aria-hidden="true">1</span>
                <h3><?php esc_html_e('1. Create a GitHub Token', 'otter-synca'); ?></h3>
            <p><?php esc_html_e('To get started, you need to create a personal access token on GitHub:', 'otter-synca'); ?></p>
            <ol>
                <li><?php esc_html_e('Go to GitHub settings', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Navigate to "Developer settings" > "Personal access tokens" > "Tokens (classic)"', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Click "Generate new token"', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Give the token a name and select "repo" permissions', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Copy the generated token and paste it in the "GitHub Token" field in the plugin settings', 'otter-synca'); ?></li>
            </ol>
            </article>

            <article class="otter-synca-help-step">
                <span class="otter-synca-help-step-number" aria-hidden="true">2</span>
                <h3><?php esc_html_e('2. Configure the Plugin', 'otter-synca'); ?></h3>
                <p><?php esc_html_e('After creating the token, configure the plugin with the following information:', 'otter-synca'); ?></p>
                <ul>
                    <li><?php esc_html_e('Repository: Repository name in the format username/repository', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Branch: Branch you want to deploy (default: main)', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Deploy Type: Select whether it is a plugin or theme', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Target Slug: Name of the plugin or theme that will be updated', 'otter-synca'); ?></li>
                </ul>
            </article>

            <article class="otter-synca-help-step">
                <span class="otter-synca-help-step-number" aria-hidden="true">3</span>
                <h3><?php esc_html_e('3. Deploy', 'otter-synca'); ?></h3>
                <p><?php esc_html_e('With everything configured, you can deploy:', 'otter-synca'); ?></p>
                <ol>
                    <li><?php esc_html_e('Verify that all settings are correct', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Click the "Deploy Now" button', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Wait for the process to complete', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Check the deploy status in the sidebar', 'otter-synca'); ?></li>
                </ol>
            </article>
        </div>
    </section>

    <section class="otter-synca-help-section" aria-labelledby="otter-synca-faq-title">
        <div class="otter-synca-help-section-heading">
            <div>
                <span class="otter-synca-help-eyebrow"><?php esc_html_e('Common questions', 'otter-synca'); ?></span>
                <h2 id="otter-synca-faq-title"><?php esc_html_e('Frequently Asked Questions', 'otter-synca'); ?></h2>
            </div>
        </div>

        <div class="otter-synca-faq">
            <details class="otter-synca-faq-item" open>
                <summary><?php esc_html_e('What permissions does the GitHub token need?', 'otter-synca'); ?></summary>
                <p><?php esc_html_e('The token needs repository access permission (repo). This allows the plugin to download code from the repository.', 'otter-synca'); ?></p>
            </details>

            <details class="otter-synca-faq-item">
                <summary><?php esc_html_e('Can I deploy multiple repositories?', 'otter-synca'); ?></summary>
                <p><?php esc_html_e('Yes! In the premium version of the plugin you can configure multiple repositories and manage them centrally.', 'otter-synca'); ?></p>
            </details>

            <details class="otter-synca-faq-item">
                <summary><?php esc_html_e('Does the plugin backup before deploying?', 'otter-synca'); ?></summary>
                <p><?php esc_html_e('In the premium version, the plugin automatically backs up before each deployment, allowing you to revert to the previous version if necessary.', 'otter-synca'); ?></p>
            </details>
        </div>
    </section>

    <section class="otter-synca-help-support">
        <span class="dashicons dashicons-sos" aria-hidden="true"></span>
        <div>
            <h2><?php esc_html_e('Need more help?', 'otter-synca'); ?></h2>
            <p><?php esc_html_e('If you didn\'t find the answer you were looking for, contact us:', 'otter-synca'); ?></p>
        </div>
        <a href="https://github.com/fernandocyfer/OtterSynca/issues" class="button button-primary" target="_blank" rel="noopener noreferrer">
            <?php esc_html_e('Open an Issue on GitHub', 'otter-synca'); ?>
        </a>
    </section>
</div>