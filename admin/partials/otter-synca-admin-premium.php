<?php
/**
 * Provide a admin area view for the plugin premium features
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

// Check if Pro addon is active
$pro_active = class_exists('Otter_Synca_Pro');
?>

<div class="otter-synca-card">
    <div class="otter-synca-card-header">
        <h2 class="otter-synca-card-title">
            <?php esc_html_e('OtterSynca Pro Add-on', 'otter-synca'); ?>
            <?php if ($pro_active): ?>
                <span class="otter-synca-pro-badge otter-synca-pro-active"><?php esc_html_e('Active', 'otter-synca'); ?></span>
            <?php else: ?>
                <span class="otter-synca-pro-badge otter-synca-pro-inactive"><?php esc_html_e('Inactive', 'otter-synca'); ?></span>
            <?php endif; ?>
        </h2>
    </div>

    <?php if ($pro_active): ?>
        <div class="otter-synca-pro-active-content">
            <p class="description">
                <?php esc_html_e('OtterSynca Pro is active! You have access to all premium features.', 'otter-synca'); ?>
            </p>
            
            <div class="otter-synca-pro-features-grid">
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Auto Deploy', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Automatic deployment via GitHub webhook', 'otter-synca'); ?></p>
                </div>
                
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Multiple Repositories', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Manage multiple repositories simultaneously', 'otter-synca'); ?></p>
                </div>
                
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Automatic Backup', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Automatic backup before each deployment', 'otter-synca'); ?></p>
                </div>
                
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Email Notifications', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Receive deployment and backup notifications', 'otter-synca'); ?></p>
                </div>
                
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Detailed Logs', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Complete history of all actions', 'otter-synca'); ?></p>
                </div>
                
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Bulk Deploy', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Deploy multiple repositories at once', 'otter-synca'); ?></p>
                </div>
            </div>
            
            <div class="otter-synca-pro-actions">
                <a href="<?php echo admin_url('admin.php?page=otter-synca-pro'); ?>" class="button button-primary">
                    <?php esc_html_e('Manage Pro', 'otter-synca'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=otter-synca-pro&tab=logs'); ?>" class="button button-secondary">
                    <?php esc_html_e('View Logs', 'otter-synca'); ?>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="otter-synca-pro-inactive-content">
            <div class="otter-synca-pro-hero">
                <h2><?php esc_html_e('Take your WordPress workflow to the next level', 'otter-synca'); ?></h2>
                <p class="description">
                    <?php esc_html_e('Automate the deployment of your plugins and themes directly from your GitHub repositories — no manual uploads and with full control over your release cycle.', 'otter-synca'); ?>
                </p>
            </div>

            <div class="otter-synca-premium-features">
                <div class="otter-synca-feature">
                    <h3>🚀 <?php esc_html_e('Auto Deploy', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Configure automatic deployments when there are new commits in the repository.', 'otter-synca'); ?></p>
                    <ul>
                        <li><?php esc_html_e('Automatic deployment via webhook', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Scheduled deployments', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Email notifications', 'otter-synca'); ?></li>
                    </ul>
                </div>

                <div class="otter-synca-feature">
                    <h3>📦 <?php esc_html_e('Multiple Repositories', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Manage multiple repositories at once.', 'otter-synca'); ?></p>
                    <ul>
                        <li><?php esc_html_e('Centralized interface', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Bulk deployment', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Repository groups', 'otter-synca'); ?></li>
                    </ul>
                </div>

                <div class="otter-synca-feature">
                    <h3>💾 <?php esc_html_e('Automatic Backup', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Keep your files safe with automatic backups.', 'otter-synca'); ?></p>
                    <ul>
                        <li><?php esc_html_e('Backup before deployment', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Easy restoration', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Version history', 'otter-synca'); ?></li>
                    </ul>
                </div>

                <div class="otter-synca-feature">
                    <h3>📊 <?php esc_html_e('Detailed Logs', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Track all actions with detailed logs.', 'otter-synca'); ?></p>
                    <ul>
                        <li><?php esc_html_e('Complete history', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Log export', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Advanced filters', 'otter-synca'); ?></li>
                    </ul>
                </div>
            </div>

            <div class="otter-synca-premium-cta">
                <div class="otter-synca-pricing-grid">
                    <div class="otter-synca-pricing-card">
                        <div class="otter-synca-pricing-header">
                            <h3><?php esc_html_e('Freelancer', 'otter-synca'); ?></h3>
                            <div class="otter-synca-price">
                                <span class="otter-synca-currency">$</span>
                                <span class="otter-synca-amount">69</span>
                                <span class="otter-synca-period">/year</span>
                            </div>
                        </div>
                        <ul class="otter-synca-benefits">
                            <li>✅ <?php esc_html_e('1 License', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Auto deploy', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Multiple repositories', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Automatic backup', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Detailed logs', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Email notifications', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Email support', 'otter-synca'); ?></li>
                        </ul>
                        <a href="https://plugins.cyfer.com.br/downloads/ottersynca-deploy-para-wordpress/" class="button button-primary" target="_blank">
                            <?php esc_html_e('Choose Plan', 'otter-synca'); ?>
                        </a>
                    </div>

                    <div class="otter-synca-pricing-card otter-synca-pricing-featured">
                        <div class="otter-synca-pricing-header">
                            <span class="otter-synca-featured-badge"><?php esc_html_e('Most Popular', 'otter-synca'); ?></span>
                            <h3><?php esc_html_e('Agency', 'otter-synca'); ?></h3>
                            <div class="otter-synca-price">
                                <span class="otter-synca-currency">$</span>
                                <span class="otter-synca-amount">129</span>
                                <span class="otter-synca-period">/year</span>
                            </div>
                        </div>
                        <ul class="otter-synca-benefits">
                            <li>✅ <?php esc_html_e('5 Licenses', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Auto deploy', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Multiple repositories', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Automatic backup', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Detailed logs', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Email notifications', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Priority support', 'otter-synca'); ?></li>
                        </ul>
                        <a href="https://plugins.cyfer.com.br/downloads/ottersynca-deploy-para-wordpress/" class="button button-primary" target="_blank">
                            <?php esc_html_e('Choose Plan', 'otter-synca'); ?>
                        </a>
                    </div>

                    <div class="otter-synca-pricing-card">
                        <div class="otter-synca-pricing-header">
                            <h3><?php esc_html_e('Unlimited', 'otter-synca'); ?></h3>
                            <div class="otter-synca-price">
                                <span class="otter-synca-currency">$</span>
                                <span class="otter-synca-amount">208</span>
                                <span class="otter-synca-period">/year</span>
                            </div>
                        </div>
                        <ul class="otter-synca-benefits">
                            <li>✅ <?php esc_html_e('Unlimited licenses', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Auto deploy', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Multiple repositories', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Automatic backup', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Detailed logs', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Email notifications', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('VIP support', 'otter-synca'); ?></li>
                        </ul>
                        <a href="https://plugins.cyfer.com.br/downloads/ottersynca-deploy-para-wordpress/" class="button button-primary" target="_blank">
                            <?php esc_html_e('Choose Plan', 'otter-synca'); ?>
                        </a>
                    </div>
                </div>
                
                <div class="otter-synca-pricing-note">
                    <p><strong><?php esc_html_e('All plans include the same Pro features, differing only in the number of available licenses.', 'otter-synca'); ?></strong></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.otter-synca-pro-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    margin-left: 10px;
}

.otter-synca-pro-badge.otter-synca-pro-active {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.otter-synca-pro-badge.otter-synca-pro-inactive {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.otter-synca-pro-hero {
    text-align: center;
    margin-bottom: 30px;
    padding: 30px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 8px;
    border: 1px solid #e1e5e9;
}

.otter-synca-pro-hero h2 {
    color: #2271b1;
    margin-bottom: 15px;
    font-size: 24px;
}

.otter-synca-pro-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.otter-synca-pro-feature {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #2271b1;
}

.otter-synca-pro-feature h3 {
    margin: 0 0 10px 0;
    color: #2271b1;
}

.otter-synca-premium-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin: 30px 0;
}

.otter-synca-feature {
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    border: 1px solid #e1e5e9;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.otter-synca-feature h3 {
    color: #2271b1;
    margin: 0 0 15px 0;
    font-size: 18px;
}

.otter-synca-feature ul {
    margin: 15px 0 0 0;
    padding-left: 20px;
}

.otter-synca-feature li {
    margin-bottom: 8px;
    color: #666;
}

.otter-synca-pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin: 30px 0;
}

.otter-synca-pricing-card {
    background: #fff;
    border: 2px solid #e1e5e9;
    border-radius: 12px;
    padding: 25px;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
}

.otter-synca-pricing-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.otter-synca-pricing-card.otter-synca-pricing-featured {
    border-color: #2271b1;
    transform: scale(1.05);
}

.otter-synca-pricing-card.otter-synca-pricing-featured:hover {
    transform: scale(1.05) translateY(-5px);
}

.otter-synca-featured-badge {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: #2271b1;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.otter-synca-pricing-header h3 {
    color: #2271b1;
    margin: 0 0 15px 0;
    font-size: 20px;
}

.otter-synca-price {
    margin-bottom: 20px;
}

.otter-synca-currency {
    font-size: 18px;
    color: #666;
    vertical-align: top;
}

.otter-synca-amount {
    font-size: 36px;
    font-weight: bold;
    color: #2271b1;
}

.otter-synca-period {
    font-size: 16px;
    color: #666;
}

.otter-synca-benefits {
    list-style: none;
    padding: 0;
    margin: 0 0 25px 0;
    text-align: left;
}

.otter-synca-benefits li {
    margin-bottom: 10px;
    color: #333;
    font-size: 14px;
}

.otter-synca-pricing-card .button {
    width: 100%;
    padding: 12px 20px;
    font-size: 16px;
    font-weight: 600;
}

.otter-synca-compatibility {
    text-align: center;
    margin-top: 20px;
    color: #666;
    font-size: 14px;
}

.otter-synca-compatibility p {
    margin: 5px 0;
}

@media (max-width: 768px) {
    .otter-synca-pricing-grid {
        grid-template-columns: 1fr;
    }
    
    .otter-synca-pricing-card.otter-synca-pricing-featured {
        transform: none;
    }
    
    .otter-synca-premium-features {
        grid-template-columns: 1fr;
    }
}
</style> 