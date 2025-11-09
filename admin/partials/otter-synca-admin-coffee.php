<?php
/**
 * Provide a admin area view for the plugin coffee support
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
        <h2 class="otter-synca-card-title"><?php esc_html_e('Buy Me a Coffee', 'otter-synca'); ?></h2>
    </div>

    <p class="description">
        <?php esc_html_e('If you like OtterSynca and want to support its development, consider buying a coffee!', 'otter-synca'); ?>
    </p>

    <div class="otter-synca-coffee-options">
        <div class="otter-synca-coffee-option">
            <h3><?php esc_html_e('One Coffee', 'otter-synca'); ?></h3>
            <div class="price">$5</div>
            <p><?php esc_html_e('A simple coffee to start the day!', 'otter-synca'); ?></p>
            <a href="https://buymeacoffee.com/cyfer" class="button button-primary" target="_blank">
                <?php esc_html_e('Buy a Coffee', 'otter-synca'); ?>
            </a>
        </div>

        <div class="otter-synca-coffee-option">
            <h3><?php esc_html_e('Double Coffee', 'otter-synca'); ?></h3>
            <div class="price">$10</div>
            <p><?php esc_html_e('Two coffees for a productive day!', 'otter-synca'); ?></p>
            <a href="https://www.buymeacoffee.com/cyfer" class="button button-primary" target="_blank">
                <?php esc_html_e('Buy Double Coffee', 'otter-synca'); ?>
            </a>
        </div>

        <div class="otter-synca-coffee-option">
            <h3><?php esc_html_e('Premium Coffee', 'otter-synca'); ?></h3>
            <div class="price">$20</div>
            <p><?php esc_html_e('A special coffee for a special day!', 'otter-synca'); ?></p>
            <a href="https://www.buymeacoffee.com/cyfer" class="button button-primary" target="_blank">
                <?php esc_html_e('Buy Premium Coffee', 'otter-synca'); ?>
            </a>
        </div>
    </div>

    <div class="otter-synca-coffee-message">
        <p>
            <?php esc_html_e('Your support is very important to keep OtterSynca development going!', 'otter-synca'); ?>
        </p>
    </div>
</div>

<style>
.otter-synca-coffee-content {
    max-width: 800px;
    margin: 20px 0;
}

.otter-synca-coffee-header {
    text-align: center;
    margin-bottom: 40px;
}

.otter-synca-coffee-options {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-bottom: 40px;
}

.otter-synca-coffee-option {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.otter-synca-coffee-option h3 {
    margin-top: 0;
    color: #2271b1;
}

.otter-synca-coffee-option .price {
    font-size: 24px;
    font-weight: bold;
    color: #2271b1;
    margin: 10px 0;
}

.otter-synca-coffee-message {
    text-align: center;
    font-style: italic;
    color: #666;
}
</style> 