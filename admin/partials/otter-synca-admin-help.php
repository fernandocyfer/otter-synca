<?php
/**
 * Provide a admin area view for the plugin help
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
        <h2 class="otter-synca-card-title"><?php esc_html_e('Primeiros Passos', 'otter-synca'); ?></h2>
    </div>

    <div class="otter-synca-help-steps">
        <div class="otter-synca-help-step">
            <h3><?php esc_html_e('1. Crie um Token do GitHub', 'otter-synca'); ?></h3>
            <p><?php esc_html_e('Para começar, você precisa criar um token de acesso pessoal no GitHub:', 'otter-synca'); ?></p>
            <ol>
                <li><?php esc_html_e('Acesse as configurações do GitHub', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Vá para "Developer settings" > "Personal access tokens"', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Clique em "Generate new token"', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Dê um nome ao token e selecione as permissões "repo"', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Copie o token gerado e cole no campo "GitHub Token" nas configurações do plugin', 'otter-synca'); ?></li>
            </ol>
        </div>

        <div class="otter-synca-help-step">
            <h3><?php esc_html_e('2. Configure o Plugin', 'otter-synca'); ?></h3>
            <p><?php esc_html_e('Após criar o token, configure o plugin com as seguintes informações:', 'otter-synca'); ?></p>
            <ul>
                <li><?php esc_html_e('Repositório: Nome do repositório no formato username/repository', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Branch: Branch que você deseja fazer deploy (padrão: main)', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Tipo de Deploy: Selecione se é um plugin ou tema', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Slug do Alvo: Nome do plugin ou tema que será atualizado', 'otter-synca'); ?></li>
            </ul>
        </div>

        <div class="otter-synca-help-step">
            <h3><?php esc_html_e('3. Faça o Deploy', 'otter-synca'); ?></h3>
            <p><?php esc_html_e('Com tudo configurado, você pode fazer o deploy:', 'otter-synca'); ?></p>
            <ol>
                <li><?php esc_html_e('Verifique se todas as configurações estão corretas', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Clique no botão "Fazer Deploy Agora"', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Aguarde o processo ser concluído', 'otter-synca'); ?></li>
                <li><?php esc_html_e('Verifique o status do deploy na barra lateral', 'otter-synca'); ?></li>
            </ol>
        </div>
    </div>
</div>

<div class="otter-synca-card">
    <div class="otter-synca-card-header">
        <h2 class="otter-synca-card-title"><?php esc_html_e('Perguntas Frequentes', 'otter-synca'); ?></h2>
    </div>

    <div class="otter-synca-faq">
        <h3><?php esc_html_e('Quais permissões o token do GitHub precisa ter?', 'otter-synca'); ?></h3>
        <p><?php esc_html_e('O token precisa ter permissão de acesso ao repositório (repo). Isso permite que o plugin baixe o código do repositório.', 'otter-synca'); ?></p>

        <h3><?php esc_html_e('Posso fazer deploy de múltiplos repositórios?', 'otter-synca'); ?></h3>
        <p><?php esc_html_e('Sim! Na versão premium do plugin você pode configurar múltiplos repositórios e gerenciá-los de forma centralizada.', 'otter-synca'); ?></p>

        <h3><?php esc_html_e('O plugin faz backup antes do deploy?', 'otter-synca'); ?></h3>
        <p><?php esc_html_e('Na versão premium, o plugin faz backup automático antes de cada deploy, permitindo reverter para a versão anterior se necessário.', 'otter-synca'); ?></p>
    </div>
</div>

<div class="otter-synca-card">
    <div class="otter-synca-card-header">
        <h2 class="otter-synca-card-title"><?php esc_html_e('Precisa de mais ajuda?', 'otter-synca'); ?></h2>
    </div>

    <p><?php esc_html_e('Se você não encontrou a resposta que procurava, entre em contato conosco:', 'otter-synca'); ?></p>
    <a href="https://github.com/fernandocyfer/OtterSynca/issues" class="button button-primary" target="_blank">
        <?php esc_html_e('Abrir um Issue no GitHub', 'otter-synca'); ?>
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