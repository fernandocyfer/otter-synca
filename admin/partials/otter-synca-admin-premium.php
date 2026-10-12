<?php
/**
 * Provide an admin area view for the plugin premium features.
 *
 * @link       https://github.com/fernandocyfer/otter-synca
 * @since      1.0.1
 *
 * @package    Otter_Synca
 * @subpackage Otter_Synca/admin/partials
 */

if (!defined('WPINC')) {
    die;
}

$pro_active = class_exists('Otter_Synca_Pro');
?>

<div class="otter-synca-premium-page">
    <section class="otter-synca-premium-hero">
        <div class="otter-synca-premium-hero-content">
            <span class="otter-synca-premium-eyebrow"><?php esc_html_e('OtterSynca Pro', 'otter-synca'); ?></span>
            <h2><?php esc_html_e('Take your WordPress workflow to the next level', 'otter-synca'); ?></h2>
            <p><?php esc_html_e('Automate the deployment of your plugins and themes directly from your GitHub repositories — no manual uploads and with full control over your release cycle.', 'otter-synca'); ?></p>

            <?php if ($pro_active): ?>
                <span class="otter-synca-premium-status otter-synca-premium-status-active">
                    <span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
                    <?php esc_html_e('Pro active', 'otter-synca'); ?>
                </span>
            <?php else: ?>
                <a href="https://plugins.cyfer.com.br/downloads/ottersynca-deploy-para-wordpress/" class="button button-primary otter-synca-premium-hero-button" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('Get OtterSynca Pro', 'otter-synca'); ?>
                </a>
            <?php endif; ?>
        </div>

        <span class="dashicons dashicons-superhero-alt otter-synca-premium-hero-icon" aria-hidden="true"></span>
    </section>

    <section class="otter-synca-premium-section" aria-labelledby="otter-synca-pro-features-title">
        <div class="otter-synca-premium-section-heading">
            <div>
                <span class="otter-synca-premium-eyebrow"><?php esc_html_e('Everything you need', 'otter-synca'); ?></span>
                <h2 id="otter-synca-pro-features-title"><?php esc_html_e('Features included in Pro', 'otter-synca'); ?></h2>
            </div>
        </div>

        <div class="otter-synca-premium-features-grid">
            <article class="otter-synca-premium-feature">
                <span class="dashicons dashicons-update-alt" aria-hidden="true"></span>
                <h3><?php esc_html_e('Auto Deploy', 'otter-synca'); ?></h3>
                <p><?php esc_html_e('Configure automatic deployments when there are new commits in the repository.', 'otter-synca'); ?></p>
                <ul>
                    <li><?php esc_html_e('Automatic deployment via webhook', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Scheduled deployments', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Email notifications', 'otter-synca'); ?></li>
                </ul>
            </article>

            <article class="otter-synca-premium-feature">
                <span class="dashicons dashicons-networking" aria-hidden="true"></span>
                <h3><?php esc_html_e('Multiple Repositories', 'otter-synca'); ?></h3>
                <p><?php esc_html_e('Manage multiple repositories at once.', 'otter-synca'); ?></p>
                <ul>
                    <li><?php esc_html_e('Centralized interface', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Bulk deployment', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Repository groups', 'otter-synca'); ?></li>
                </ul>
            </article>

            <article class="otter-synca-premium-feature">
                <span class="dashicons dashicons-backup" aria-hidden="true"></span>
                <h3><?php esc_html_e('Automatic Backup', 'otter-synca'); ?></h3>
                <p><?php esc_html_e('Keep your files safe with automatic backups.', 'otter-synca'); ?></p>
                <ul>
                    <li><?php esc_html_e('Backup before deployment', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Easy restoration', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Version history', 'otter-synca'); ?></li>
                </ul>
            </article>

            <article class="otter-synca-premium-feature">
                <span class="dashicons dashicons-chart-area" aria-hidden="true"></span>
                <h3><?php esc_html_e('Detailed Logs', 'otter-synca'); ?></h3>
                <p><?php esc_html_e('Track all actions with detailed logs.', 'otter-synca'); ?></p>
                <ul>
                    <li><?php esc_html_e('Complete history', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Log export', 'otter-synca'); ?></li>
                    <li><?php esc_html_e('Advanced filters', 'otter-synca'); ?></li>
                </ul>
            </article>
        </div>
    </section>

    <?php if ($pro_active): ?>
        <section class="otter-synca-premium-action">
            <span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
            <div>
                <h2><?php esc_html_e('OtterSynca Pro is active', 'otter-synca'); ?></h2>
                <p><?php esc_html_e('You have access to all premium features.', 'otter-synca'); ?></p>
            </div>
            <div class="otter-synca-premium-action-buttons">
                <a href="<?php echo esc_url(admin_url('admin.php?page=otter-synca-pro')); ?>" class="button button-primary">
                    <?php esc_html_e('Manage Pro', 'otter-synca'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=otter-synca-pro&tab=logs')); ?>" class="button button-secondary">
                    <?php esc_html_e('View Logs', 'otter-synca'); ?>
                </a>
            </div>
        </section>
    <?php else: ?>
        <section class="otter-synca-premium-action otter-synca-premium-offer">
            <span class="dashicons dashicons-superhero-alt" aria-hidden="true"></span>
            <div>
                <span class="otter-synca-premium-eyebrow"><?php esc_html_e('Upgrade your workflow', 'otter-synca'); ?></span>
                <h2><?php esc_html_e('Unlock OtterSynca Pro', 'otter-synca'); ?></h2>
                <p><?php esc_html_e('Unlock every Pro feature and automate your deployment workflow.', 'otter-synca'); ?></p>
            </div>
            <a href="https://plugins.cyfer.com.br/downloads/ottersynca-deploy-para-wordpress/" class="button button-primary" target="_blank" rel="noopener noreferrer">
                <?php esc_html_e('Get OtterSynca Pro', 'otter-synca'); ?>
            </a>
        </section>
    <?php endif; ?>
</div>
