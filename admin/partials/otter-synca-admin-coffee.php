<?php
/**
 * Provide a admin area view for the plugin coffee support
 *
 * @link       https://github.com/fernandofilho
 * @since      1.0.0
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
        <?php esc_html_e('Se você gosta do OtterSynca e quer apoiar seu desenvolvimento, considere comprar um café!', 'otter-synca'); ?>
    </p>

    <div class="otter-synca-coffee-options">
        <div class="otter-synca-coffee-option">
            <h3><?php esc_html_e('Um Café', 'otter-synca'); ?></h3>
            <div class="price">R$ 5,00</div>
            <p><?php esc_html_e('Um café simples para começar o dia!', 'otter-synca'); ?></p>
            <a href="https://www.buymeacoffee.com/fernandofilho" class="button button-primary" target="_blank">
                <?php esc_html_e('Comprar um Café', 'otter-synca'); ?>
            </a>
        </div>

        <div class="otter-synca-coffee-option">
            <h3><?php esc_html_e('Café Duplo', 'otter-synca'); ?></h3>
            <div class="price">R$ 10,00</div>
            <p><?php esc_html_e('Dois cafés para um dia produtivo!', 'otter-synca'); ?></p>
            <a href="https://www.buymeacoffee.com/fernandofilho" class="button button-primary" target="_blank">
                <?php esc_html_e('Comprar Café Duplo', 'otter-synca'); ?>
            </a>
        </div>

        <div class="otter-synca-coffee-option">
            <h3><?php esc_html_e('Café Premium', 'otter-synca'); ?></h3>
            <div class="price">R$ 20,00</div>
            <p><?php esc_html_e('Um café especial para um dia especial!', 'otter-synca'); ?></p>
            <a href="https://www.buymeacoffee.com/fernandofilho" class="button button-primary" target="_blank">
                <?php esc_html_e('Comprar Café Premium', 'otter-synca'); ?>
            </a>
        </div>
    </div>

    <div class="otter-synca-coffee-message">
        <p>
            <?php esc_html_e('Seu apoio é muito importante para manter o desenvolvimento do OtterSynca!', 'otter-synca'); ?>
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