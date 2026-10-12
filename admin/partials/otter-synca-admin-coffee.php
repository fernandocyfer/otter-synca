<?php
/**
 * Provide an admin area view for plugin support.
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
?>

<div class="otter-synca-coffee-page">
    <section class="otter-synca-coffee-hero">
        <div>
            <span class="otter-synca-coffee-eyebrow"><?php esc_html_e('Support OtterSynca', 'otter-synca'); ?></span>
            <h2><?php esc_html_e('Buy Me a Coffee', 'otter-synca'); ?></h2>
            <p><?php esc_html_e('If you like OtterSynca and want to support its development, consider buying a coffee!', 'otter-synca'); ?></p>
        </div>
        <span class="dashicons dashicons-coffee" aria-hidden="true"></span>
    </section>

    <section class="otter-synca-coffee-section" aria-labelledby="otter-synca-coffee-options-title">
        <div class="otter-synca-coffee-section-heading">
            <span class="otter-synca-coffee-eyebrow"><?php esc_html_e('Choose your support', 'otter-synca'); ?></span>
            <h2 id="otter-synca-coffee-options-title"><?php esc_html_e('Every coffee helps', 'otter-synca'); ?></h2>
        </div>

        <div class="otter-synca-coffee-options">
            <article class="otter-synca-coffee-option">
                <span class="dashicons dashicons-coffee" aria-hidden="true"></span>
                <h3><?php esc_html_e('One Coffee', 'otter-synca'); ?></h3>
                <div class="otter-synca-coffee-price">$5</div>
                <p><?php esc_html_e('A simple coffee to start the day!', 'otter-synca'); ?></p>
                <a href="https://buymeacoffee.com/cyfer" class="button button-primary" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('Buy a Coffee', 'otter-synca'); ?>
                </a>
            </article>

            <article class="otter-synca-coffee-option otter-synca-coffee-option-featured">
                <span class="otter-synca-coffee-double-icon" aria-hidden="true">
                    <span class="dashicons dashicons-coffee"></span>
                    <span class="dashicons dashicons-coffee"></span>
                </span>
                <h3><?php esc_html_e('Double Coffee', 'otter-synca'); ?></h3>
                <div class="otter-synca-coffee-price">$10</div>
                <p><?php esc_html_e('Two coffees for a productive day!', 'otter-synca'); ?></p>
                <a href="https://buymeacoffee.com/cyfer" class="button button-primary" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('Buy Double Coffee', 'otter-synca'); ?>
                </a>
            </article>

            <article class="otter-synca-coffee-option">
                <span class="dashicons dashicons-heart" aria-hidden="true"></span>
                <h3><?php esc_html_e('Premium Coffee', 'otter-synca'); ?></h3>
                <div class="otter-synca-coffee-price">$20</div>
                <p><?php esc_html_e('A special coffee for a special day!', 'otter-synca'); ?></p>
                <a href="https://buymeacoffee.com/cyfer" class="button button-primary" target="_blank" rel="noopener noreferrer">
                    <?php esc_html_e('Buy Premium Coffee', 'otter-synca'); ?>
                </a>
            </article>
        </div>
    </section>

    <section class="otter-synca-coffee-thanks">
        <span class="dashicons dashicons-heart" aria-hidden="true"></span>
        <div>
            <h2><?php esc_html_e('Thank you for your support!', 'otter-synca'); ?></h2>
            <p><?php esc_html_e('Your support is very important to keep OtterSynca development going!', 'otter-synca'); ?></p>
        </div>
    </section>
</div>
