<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Otter_Synca
 * @subpackage Otter_Synca/admin
 */
class Otter_Synca_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) 
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Register AJAX actions
        add_action('wp_ajax_otter_synca_deploy', array($this, 'handle_deploy'));
        add_action('wp_ajax_otter_synca_test_webhook', array($this, 'test_webhook'));
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() 
    {
        // Carrega o CSS apenas nas páginas específicas do plugin
        $screen = get_current_screen();
        if (!$screen || !in_array($screen->id, array('toplevel_page_otter-synca', 'otter-synca_page_otter-synca-premium', 'otter-synca_page_otter-synca-help', 'otter-synca_page_otter-synca-coffee'))) {
            return;
        }

        wp_enqueue_style (
            $this->plugin_name, 
            plugin_dir_url(__FILE__) . 'css/otter-synca-admin.css', 
            array(), 
            $this->version, 
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() 
    {
        // Carrega o JavaScript apenas nas páginas específicas do plugin
        $screen = get_current_screen();
        if (!$screen || !in_array($screen->id, array('toplevel_page_otter-synca', 'otter-synca_page_otter-synca-premium', 'otter-synca_page_otter-synca-help', 'otter-synca_page_otter-synca-coffee'))) {
            return;
        }

        wp_enqueue_script (
            $this->plugin_name, 
            plugin_dir_url(__FILE__) . 'js/otter-synca-admin.js', 
            array('jquery'), 
            $this->version, 
            false
        );
        
        // Add localized script data
        wp_localize_script($this->plugin_name, 'otter_synca_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('otter_synca_nonce'),
            'deploying' => __('Deploying...', 'otter-synca'),
            'success' => __('Deploy successful!', 'otter-synca'),
            'error' => __('Deploy failed!', 'otter-synca'),
            'success_text' => __('Success', 'otter-synca'),
            'error_text' => __('Error', 'otter-synca'),
            'deployed_at_text' => __('Deployed at:', 'otter-synca'),
            'required_fields_message' => __('Please fill in all required fields.', 'otter-synca'),
            'invalid_repository_message' => __('Invalid repository format. Use format: owner/repository', 'otter-synca'),
            'manual_deploy_message' => __('O deploy só pode ser iniciado manualmente.', 'otter-synca'),
            'debug' => WP_DEBUG
        ));
    }

    /**
     * Add menu items to the admin area.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu() 
    {
        add_menu_page(
            __('OtterSynca', 'otter-synca'),
            __('OtterSynca', 'otter-synca'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_admin_page'),
            'dashicons-update',
            100
        );
    }

    /**
     * Register plugin settings.
     *
     * @since    1.0.0
     */
    public function register_settings() 
    {
        register_setting('otter_synca_options', 'otter_synca_github_token');
        register_setting('otter_synca_options', 'otter_synca_repository');
        register_setting('otter_synca_options', 'otter_synca_branch');
        register_setting('otter_synca_options', 'otter_synca_deploy_type');
        register_setting('otter_synca_options', 'otter_synca_target_slug');
        register_setting('otter_synca_options', 'otter_synca_auto_deploy');
        register_setting('otter_synca_options', 'otter_synca_webhook_secret');
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page() 
    {
        include_once 'partials/otter-synca-admin-display.php';
    }

    /**
     * Handle the deploy action via AJAX.
     *
     * @since    1.0.0
     */
    public function handle_deploy() {
        // Verifica o nonce primeiro
        if (!check_ajax_referer('otter_synca_nonce', 'nonce', false)) {
            wp_send_json_error(__('Erro de segurança: nonce inválido.', 'otter-synca'));
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Você não tem permissões suficientes para acessar esta página.', 'otter-synca'));
            return;
        }

        // Verifica se a requisição veio do botão de deploy
        if (!isset($_POST['action']) || $_POST['action'] !== 'otter_synca_deploy') {
            wp_send_json_error(__('Requisição inválida.', 'otter-synca'));
            return;
        }

        // Verifica se o deploy foi iniciado manualmente
        if (!isset($_POST['manual_deploy']) || $_POST['manual_deploy'] !== 'true') {
            wp_send_json_error(__('O deploy só pode ser iniciado manualmente através do botão.', 'otter-synca'));
            return;
        }

        // Verifica se o evento foi realmente um clique
        if (!isset($_POST['event_type']) || $_POST['event_type'] !== 'click') {
            wp_send_json_error(__('O deploy só pode ser iniciado através de um clique manual.', 'otter-synca'));
            return;
        }

        // Verifica se a requisição veio de um clique real
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            wp_send_json_error(__('Requisição inválida.', 'otter-synca'));
            return;
        }

        // Verifica se a requisição veio do formulário correto
        if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], admin_url('admin.php?page=otter-synca')) === false) {
            wp_send_json_error(__('Requisição inválida. O deploy só pode ser iniciado a partir da página do plugin.', 'otter-synca'));
            return;
        }

        // Verifica se o usuário está logado e tem permissão
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'otter-synca'));
            return;
        }

        // Verifica se não há outro deploy em andamento
        $last_deploy = get_option('otter_synca_last_deploy');
        if (!empty($last_deploy) && isset($last_deploy['timestamp'])) {
            $last_deploy_time = strtotime($last_deploy['timestamp']);
            $current_time = time();
            if ($current_time - $last_deploy_time < 30) { // 30 segundos de intervalo mínimo
                wp_send_json_error(__('Aguarde 30 segundos antes de iniciar um novo deploy.', 'otter-synca'));
                return;
            }
        }

        $github_token = get_option('otter_synca_github_token');
        $repository = get_option('otter_synca_repository');
        $branch = get_option('otter_synca_branch');
        $deploy_type = get_option('otter_synca_deploy_type');
        $target_slug = get_option('otter_synca_target_slug');

        if (empty($github_token) || empty($repository) || empty($target_slug)) {
            wp_send_json_error(__('Please fill in all required fields.', 'otter-synca'));
        }

        try {
            // Verifica se a branch existe e obtém o SHA do último commit
            $branch_url = "https://api.github.com/repos/{$repository}/branches/{$branch}";
            $branch_response = wp_remote_get($branch_url, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $github_token,
                    'Accept' => 'application/vnd.github.v3+json'
                )
            ));

            if (is_wp_error($branch_response)) {
                throw new Exception($branch_response->get_error_message());
            }

            $branch_status = wp_remote_retrieve_response_code($branch_response);
            
            if ($branch_status !== 200) {
                /* translators: %s: branch name */
                throw new Exception(sprintf(__('Branch "%s" não encontrada no repositório. Verifique se a branch está correta e se você tem permissão para acessá-la.', 'otter-synca'), $branch));
            }

            $branch_data = json_decode(wp_remote_retrieve_body($branch_response), true);
            if (empty($branch_data) || !isset($branch_data['commit']['sha'])) {
                throw new Exception(__('Não foi possível obter informações da branch.', 'otter-synca'));
            }

            $commit_sha = $branch_data['commit']['sha'];

            // Download repository ZIP usando o SHA do commit
            $zip_url = "https://api.github.com/repos/{$repository}/zipball/{$commit_sha}";
            $response = wp_remote_get($zip_url, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $github_token,
                    'Accept' => 'application/vnd.github.v3+json'
                )
            ));

            if (is_wp_error($response)) {
                throw new Exception($response->get_error_message());
            }

            $zip_content = wp_remote_retrieve_body($response);
            if (empty($zip_content)) {
                throw new Exception(__('Falha ao baixar o repositório.', 'otter-synca'));
            }

            // Verifica o conteúdo do ZIP
            $temp_dir = get_temp_dir();
            $zip_file = $temp_dir . 'otter-synca-temp.zip';
            file_put_contents($zip_file, $zip_content);

            // Extract ZIP file
            $zip = new ZipArchive;
            if ($zip->open($zip_file) === TRUE) {
                $extract_path = $temp_dir . 'otter-synca-extract/';
                $zip->extractTo($extract_path);
                $zip->close();

                // Get the first directory (GitHub adds a hash to the directory name)
                $extracted_dirs = glob($extract_path . '*', GLOB_ONLYDIR);
                if (empty($extracted_dirs)) {
                    throw new Exception(__('Falha ao extrair o repositório.', 'otter-synca'));
                }
                $source_dir = $extracted_dirs[0];

                // Verifica se o commit SHA corresponde
                $git_head_file = $source_dir . '/.git/HEAD';
                if (file_exists($git_head_file)) {
                    $git_head = file_get_contents($git_head_file);
                    if (strpos($git_head, $commit_sha) === false) {
                        /* translators: %s: branch name */
                        throw new Exception(sprintf(__('O conteúdo baixado não corresponde ao commit da branch "%s".', 'otter-synca'), $branch));
                    }
                }

                // Determine target directory
                $target_dir = $deploy_type === 'plugin' 
                    ? WP_PLUGIN_DIR . '/' . $target_slug
                    : WP_CONTENT_DIR . '/themes/' . $target_slug;

                // Copy files
                $this->copy_directory($source_dir, $target_dir);

                // Clean up
                wp_delete_file($zip_file);
                $this->remove_directory($extract_path);

                // Update last deploy info
                update_option('otter_synca_last_deploy', array(
                    'status' => 'success',
                    'message' => __('Deploy completed successfully.', 'otter-synca'),
                    'timestamp' => current_time('mysql')
                ));

                wp_send_json_success(__('Deploy completed successfully.', 'otter-synca'));
            } else {
                throw new Exception(__('Failed to open ZIP file.', 'otter-synca'));
            }
        } catch (Exception $e) {
            update_option('otter_synca_last_deploy', array(
                'status' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => current_time('mysql')
            ));
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * Copy a directory recursively.
     *
     * @since    1.0.0
     * @param    string    $source    Source directory
     * @param    string    $dest      Destination directory
     */
    private function copy_directory($source, $dest) {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }

        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $src = $source . '/' . $file;
                $dst = $dest . '/' . $file;

                if (is_dir($src)) {
                    $this->copy_directory($src, $dst);
                } else {
                    copy($src, $dst);
                }
            }
        }
        closedir($dir);
    }

    /**
     * Remove a directory recursively.
     *
     * @since    1.0.0
     * @param    string    $dir    Directory to remove
     */
    private function remove_directory($dir) {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->remove_directory($path) : wp_delete_file($path);
        }
        return rmdir($dir);
    }

    /**
     * Handle the test webhook action via AJAX.
     *
     * @since    1.0.0
     */
    public function test_webhook() {
        // Verifica o nonce primeiro
        if (!check_ajax_referer('otter_synca_nonce', 'nonce', false)) {
            wp_send_json_error(__('Erro de segurança: nonce inválido.', 'otter-synca'));
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Você não tem permissões suficientes para acessar esta página.', 'otter-synca'));
            return;
        }

        // Verifica se a requisição veio do botão de testar o webhook
        if (!isset($_POST['action']) || $_POST['action'] !== 'otter_synca_test_webhook') {
            wp_send_json_error(__('Requisição inválida.', 'otter-synca'));
            return;
        }

        // Verifica se o usuário está logado e tem permissão
        if (!is_user_logged_in() || !current_user_can('manage_options')) {
            wp_send_json_error(__('Você não tem permissão para realizar esta ação.', 'otter-synca'));
            return;
        }

        // Verifica se não há outro teste de webhook em andamento
        $last_test = get_option('otter_synca_last_test');
        if (!empty($last_test) && isset($last_test['timestamp'])) {
            $last_test_time = strtotime($last_test['timestamp']);
            $current_time = time();
            if ($current_time - $last_test_time < 30) { // 30 segundos de intervalo mínimo
                wp_send_json_error(__('Aguarde 30 segundos antes de iniciar um novo teste de webhook.', 'otter-synca'));
                return;
            }
        }

        $webhook_secret = get_option('otter_synca_webhook_secret');

        if (empty($webhook_secret)) {
            wp_send_json_error(__('Please fill in the webhook secret.', 'otter-synca'));
        }

        try {
            // Gera um payload de teste
            $payload = json_encode(array(
                'action' => 'test',
                'repository' => 'owner/repository',
                'branch' => 'main',
                'commit_sha' => 'test-commit-sha'
            ));

            // Gera o hash do payload usando o secret do webhook
            $signature = hash_hmac('sha256', $payload, $webhook_secret);

            // Cria o cabeçalho de autenticação
            $headers = array(
                'Content-Type: application/json',
                'X-Hub-Signature-256: ' . $signature
            );

            // Envia uma requisição POST para o webhook
            $response = wp_remote_post('https://example.com/webhook', array(
                'headers' => $headers,
                'body' => $payload
            ));

            if (is_wp_error($response)) {
                throw new Exception($response->get_error_message());
            }

            $response_code = wp_remote_retrieve_response_code($response);
            
            if ($response_code !== 200) {
                throw new Exception(__('Webhook test failed. Please check your webhook configuration.', 'otter-synca'));
            }

            // Update last test info
            update_option('otter_synca_last_test', array(
                'status' => 'success',
                'message' => __('Webhook test successful.', 'otter-synca'),
                'timestamp' => current_time('mysql')
            ));

            wp_send_json_success(__('Webhook test successful.', 'otter-synca'));
        } catch (Exception $e) {
            update_option('otter_synca_last_test', array(
                'status' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => current_time('mysql')
            ));
            wp_send_json_error($e->getMessage());
        }
    }
} 