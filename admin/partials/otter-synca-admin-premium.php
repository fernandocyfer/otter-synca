<?php
/**
 * Provide a admin area view for the plugin premium features
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

// Check if Pro addon is active
$pro_active = class_exists('Otter_Synca_Pro');
?>

<div class="otter-synca-card">
    <div class="otter-synca-card-header">
        <h2 class="otter-synca-card-title">
            <?php esc_html_e('OtterSynca Pro Add-on', 'otter-synca'); ?>
            <?php if ($pro_active): ?>
                <span class="otter-synca-pro-badge otter-synca-pro-active"><?php esc_html_e('Ativo', 'otter-synca'); ?></span>
            <?php else: ?>
                <span class="otter-synca-pro-badge otter-synca-pro-inactive"><?php esc_html_e('Inativo', 'otter-synca'); ?></span>
            <?php endif; ?>
        </h2>
    </div>

    <?php if ($pro_active): ?>
        <div class="otter-synca-pro-active-content">
            <p class="description">
                <?php esc_html_e('OtterSynca Pro está ativo! Você tem acesso a todos os recursos premium.', 'otter-synca'); ?>
            </p>
            
            <div class="otter-synca-pro-features-grid">
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Deploy Automático', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Deploy automático via webhook do GitHub', 'otter-synca'); ?></p>
                </div>
                
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Múltiplos Repositórios', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Gerencie vários repositórios simultaneamente', 'otter-synca'); ?></p>
                </div>
                
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Backup Automático', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Backup automático antes de cada deploy', 'otter-synca'); ?></p>
                </div>
                
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Notificações por Email', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Receba notificações de deploy e backup', 'otter-synca'); ?></p>
                </div>
                
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Logs Detalhados', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Histórico completo de todas as ações', 'otter-synca'); ?></p>
                </div>
                
                <div class="otter-synca-pro-feature">
                    <h3>✅ <?php esc_html_e('Deploy em Massa', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Deploy de múltiplos repositórios de uma vez', 'otter-synca'); ?></p>
                </div>
            </div>
            
            <div class="otter-synca-pro-actions">
                <a href="<?php echo admin_url('admin.php?page=otter-synca-pro'); ?>" class="button button-primary">
                    <?php esc_html_e('Gerenciar Pro', 'otter-synca'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=otter-synca-pro&tab=logs'); ?>" class="button button-secondary">
                    <?php esc_html_e('Ver Logs', 'otter-synca'); ?>
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="otter-synca-pro-inactive-content">
            <div class="otter-synca-pro-hero">
                <h2><?php esc_html_e('Leve seu fluxo de trabalho WordPress para o próximo nível', 'otter-synca'); ?></h2>
                <p class="description">
                    <?php esc_html_e('Automatize a implantação de seus plugins e temas diretamente dos seus repositórios do GitHub — sem uploads manuais e com controle total sobre seu ciclo de lançamento.', 'otter-synca'); ?>
                </p>
            </div>

            <div class="otter-synca-premium-features">
                <div class="otter-synca-feature">
                    <h3>🚀 <?php esc_html_e('Deploy Automático', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Configure deploys automáticos quando houver novos commits no repositório.', 'otter-synca'); ?></p>
                    <ul>
                        <li><?php esc_html_e('Deploy automático por webhook', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Agendamento de deploys', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Notificações por email', 'otter-synca'); ?></li>
                    </ul>
                </div>

                <div class="otter-synca-feature">
                    <h3>📦 <?php esc_html_e('Múltiplos Repositórios', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Gerencie vários repositórios de uma vez.', 'otter-synca'); ?></p>
                    <ul>
                        <li><?php esc_html_e('Interface centralizada', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Deploy em massa', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Grupos de repositórios', 'otter-synca'); ?></li>
                    </ul>
                </div>

                <div class="otter-synca-feature">
                    <h3>💾 <?php esc_html_e('Backup Automático', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Mantenha seus arquivos seguros com backups automáticos.', 'otter-synca'); ?></p>
                    <ul>
                        <li><?php esc_html_e('Backup antes do deploy', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Restauração fácil', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Histórico de versões', 'otter-synca'); ?></li>
                    </ul>
                </div>

                <div class="otter-synca-feature">
                    <h3>📊 <?php esc_html_e('Logs Detalhados', 'otter-synca'); ?></h3>
                    <p><?php esc_html_e('Acompanhe todas as ações com logs detalhados.', 'otter-synca'); ?></p>
                    <ul>
                        <li><?php esc_html_e('Histórico completo', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Exportação de logs', 'otter-synca'); ?></li>
                        <li><?php esc_html_e('Filtros avançados', 'otter-synca'); ?></li>
                    </ul>
                </div>
            </div>

            <div class="otter-synca-premium-cta">
                <div class="otter-synca-pricing-grid">
                    <div class="otter-synca-pricing-card">
                        <div class="otter-synca-pricing-header">
                            <h3><?php esc_html_e('Freelancer', 'otter-synca'); ?></h3>
                            <div class="otter-synca-price">
                                <span class="otter-synca-currency">R$</span>
                                <span class="otter-synca-amount">249</span>
                                <span class="otter-synca-period">/ano</span>
                            </div>
                        </div>
                        <ul class="otter-synca-benefits">
                            <li>✅ <?php esc_html_e('1 Licença', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Deploy automático', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Múltiplos repositórios', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Backup automático', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Logs detalhados', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Notificações por email', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Suporte por email', 'otter-synca'); ?></li>
                        </ul>
                        <a href="https://plugins.cyfer.com.br/downloads/ottersynca-deploy-para-wordpress/" class="button button-primary" target="_blank">
                            <?php esc_html_e('Escolher Plano', 'otter-synca'); ?>
                        </a>
                    </div>

                    <div class="otter-synca-pricing-card otter-synca-pricing-featured">
                        <div class="otter-synca-pricing-header">
                            <span class="otter-synca-featured-badge"><?php esc_html_e('Mais Popular', 'otter-synca'); ?></span>
                            <h3><?php esc_html_e('Agência', 'otter-synca'); ?></h3>
                            <div class="otter-synca-price">
                                <span class="otter-synca-currency">R$</span>
                                <span class="otter-synca-amount">499</span>
                                <span class="otter-synca-period">/ano</span>
                            </div>
                        </div>
                        <ul class="otter-synca-benefits">
                            <li>✅ <?php esc_html_e('5 Licenças', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Deploy automático', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Múltiplos repositórios', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Backup automático', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Logs detalhados', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Notificações por email', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Suporte prioritário', 'otter-synca'); ?></li>
                        </ul>
                        <a href="https://plugins.cyfer.com.br/downloads/ottersynca-deploy-para-wordpress/" class="button button-primary" target="_blank">
                            <?php esc_html_e('Escolher Plano', 'otter-synca'); ?>
                        </a>
                    </div>

                    <div class="otter-synca-pricing-card">
                        <div class="otter-synca-pricing-header">
                            <h3><?php esc_html_e('Ilimitado', 'otter-synca'); ?></h3>
                            <div class="otter-synca-price">
                                <span class="otter-synca-currency">R$</span>
                                <span class="otter-synca-amount">999</span>
                                <span class="otter-synca-period">/ano</span>
                            </div>
                        </div>
                        <ul class="otter-synca-benefits">
                            <li>✅ <?php esc_html_e('Licenças ilimitadas', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Deploy automático', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Múltiplos repositórios', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Backup automático', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Logs detalhados', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Notificações por email', 'otter-synca'); ?></li>
                            <li>✅ <?php esc_html_e('Suporte VIP', 'otter-synca'); ?></li>
                        </ul>
                        <a href="https://plugins.cyfer.com.br/downloads/ottersynca-deploy-para-wordpress/" class="button button-primary" target="_blank">
                            <?php esc_html_e('Escolher Plano', 'otter-synca'); ?>
                        </a>
                    </div>
                </div>
                
                <div class="otter-synca-pricing-note">
                    <p><strong><?php esc_html_e('Todos os planos incluem as mesmas funcionalidades Pro, diferenciando apenas na quantidade de licenças disponíveis.', 'otter-synca'); ?></strong></p>
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