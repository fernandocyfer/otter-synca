<?php
/**
 * Admin-specific functionality.
 *
 * @since      1.0.1
 * @package    Otter_Synca
 * @subpackage Otter_Synca/admin
 */

class Otter_Synca_Admin {

    private $plugin_name;
    private $version;

    /**
     * Initialize admin hooks.
     *
     * @param string $plugin_name Plugin identifier.
     * @param string $version     Plugin version.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('wp_ajax_otter_synca_deploy', array($this, 'handle_deploy'));
        add_action('wp_ajax_otter_synca_test_webhook', array($this, 'test_webhook'));
    }

    /**
     * Enqueue admin styles only on OtterSynca screens.
     */
    public function enqueue_styles() {
        if (!$this->is_plugin_screen()) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/otter-synca-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Enqueue admin scripts only on OtterSynca screens.
     */
    public function enqueue_scripts() {
        if (!$this->is_plugin_screen()) {
            return;
        }

        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/otter-synca-admin.js',
            array('jquery'),
            $this->version,
            true
        );

        wp_localize_script(
            $this->plugin_name,
            'otter_synca_admin',
            array(
                'ajax_url'                   => admin_url('admin-ajax.php'),
                'nonce'                      => wp_create_nonce('otter_synca_nonce'),
                'deploying'                  => __('Deploying...', 'otter-synca'),
                'success'                    => __('Deploy successful!', 'otter-synca'),
                'error'                      => __('Deploy failed!', 'otter-synca'),
                'success_text'               => __('Success', 'otter-synca'),
                'error_text'                 => __('Error', 'otter-synca'),
                'deployed_at_text'           => __('Deployed at:', 'otter-synca'),
                'required_fields_message'    => __('Please fill in all required fields.', 'otter-synca'),
                'invalid_repository_message' => __('Invalid repository format. Use owner/repository.', 'otter-synca'),
                'show_token'                 => __('Show Token', 'otter-synca'),
                'hide_token'                 => __('Hide Token', 'otter-synca'),
                'show_secret'                => __('Show Secret', 'otter-synca'),
                'hide_secret'                => __('Hide Secret', 'otter-synca'),
                'secret_generated'           => __('Webhook secret generated successfully.', 'otter-synca'),
                'validating_webhook'         => __('Validating...', 'otter-synca'),
                'validate_webhook'           => __('Validate Webhook', 'otter-synca'),
                'webhook_error'              => __('Could not validate the webhook configuration.', 'otter-synca'),
            )
        );
    }

    /**
     * Add the plugin menu.
     */
    public function add_plugin_admin_menu() {
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
     * Register settings with server-side validation.
     */
    public function register_settings() {
        register_setting('otter_synca_options', 'otter_synca_github_token', array(
            'sanitize_callback' => array($this, 'sanitize_token'),
        ));
        register_setting('otter_synca_options', 'otter_synca_repository', array(
            'sanitize_callback' => array($this, 'sanitize_repository'),
        ));
        register_setting('otter_synca_options', 'otter_synca_branch', array(
            'sanitize_callback' => array($this, 'sanitize_branch'),
        ));
        register_setting('otter_synca_options', 'otter_synca_deploy_type', array(
            'sanitize_callback' => array($this, 'sanitize_deploy_type'),
        ));
        register_setting('otter_synca_options', 'otter_synca_target_slug', array(
            'sanitize_callback' => array($this, 'sanitize_target_slug'),
        ));
        register_setting('otter_synca_options', 'otter_synca_auto_deploy', array(
            'sanitize_callback' => array($this, 'sanitize_auto_deploy'),
        ));
        register_setting('otter_synca_options', 'otter_synca_webhook_secret', array(
            'sanitize_callback' => array($this, 'sanitize_webhook_secret'),
        ));

        $this->migrate_credentials();
    }

    /**
     * Render the main admin page.
     */
    public function display_plugin_admin_page() {
        include_once 'partials/otter-synca-admin-display.php';
    }

    /**
     * Deploy the configured repository.
     */
    public function handle_deploy() {
        if (!check_ajax_referer('otter_synca_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => __('Security error: invalid nonce.', 'otter-synca')), 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'otter-synca')), 403);
        }

        $token = (string) get_option('otter_synca_github_token', '');
        $repository = (string) get_option('otter_synca_repository', '');
        $branch = (string) get_option('otter_synca_branch', 'main');
        $deploy_type = (string) get_option('otter_synca_deploy_type', 'plugin');
        $target_slug = (string) get_option('otter_synca_target_slug', '');

        try {
            $commit_sha = Otter_Synca_Deployer::resolve_commit($repository, $branch, $token);
            Otter_Synca_Deployer::deploy($repository, $commit_sha, $token, $deploy_type, $target_slug);

            $message = __('Deploy completed successfully.', 'otter-synca');
            update_option('otter_synca_last_deploy', array(
                'status'     => 'success',
                'message'    => $message,
                'timestamp'  => current_time('mysql'),
                'commit_sha' => $commit_sha,
                'branch'     => $branch,
            ));

            wp_send_json_success(array(
                'message'    => $message,
                'commit_sha' => $commit_sha,
            ));
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            update_option('otter_synca_last_deploy', array(
                'status'    => 'error',
                'message'   => $message,
                'timestamp' => current_time('mysql'),
            ));

            wp_send_json_error(array('message' => $message));
        }
    }

    /**
     * Validate webhook settings without sending secrets to third parties.
     */
    public function test_webhook() {
        if (!check_ajax_referer('otter_synca_nonce', 'nonce', false)) {
            wp_send_json_error(array('message' => __('Security error: invalid nonce.', 'otter-synca')), 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'otter-synca')), 403);
        }

        if (!class_exists('Otter_Synca_Pro')) {
            wp_send_json_error(array('message' => __('OtterSynca Pro must be active to use webhooks.', 'otter-synca')));
        }

        if (!get_option('otter_synca_auto_deploy', false)) {
            wp_send_json_error(array('message' => __('Enable auto deploy before validating the webhook.', 'otter-synca')));
        }

        $secret = (string) get_option('otter_synca_webhook_secret', '');
        if (strlen($secret) < 32) {
            wp_send_json_error(array('message' => __('Configure a webhook secret with at least 32 characters.', 'otter-synca')));
        }

        try {
            Otter_Synca_Deployer::validate_repository((string) get_option('otter_synca_repository', ''));
            Otter_Synca_Deployer::validate_branch((string) get_option('otter_synca_branch', 'main'));
        } catch (Exception $exception) {
            wp_send_json_error(array('message' => $exception->getMessage()));
        }

        wp_send_json_success(array(
            'message' => __('Webhook configuration is valid.', 'otter-synca'),
            'url'     => Otter_Synca_Webhook::get_webhook_url(),
        ));
    }

    /**
     * Sanitize GitHub token.
     *
     * @param mixed $value Input value.
     * @return string
     */
    public function sanitize_token($value) {
        $value = trim(sanitize_text_field((string) $value));
        return Otter_Synca_Deployer::encrypt_secret($value);
    }

    /**
     * Sanitize repository while preserving the previous valid value on error.
     *
     * @param mixed $value Input value.
     * @return string
     */
    public function sanitize_repository($value) {
        $value = trim((string) $value);
        return $this->validated_setting(
            'otter_synca_repository',
            $value,
            array('Otter_Synca_Deployer', 'validate_repository')
        );
    }

    /**
     * Sanitize branch.
     *
     * @param mixed $value Input value.
     * @return string
     */
    public function sanitize_branch($value) {
        $value = trim((string) $value);
        return $this->validated_setting(
            'otter_synca_branch',
            $value,
            array('Otter_Synca_Deployer', 'validate_branch')
        );
    }

    /**
     * Sanitize target slug.
     *
     * @param mixed $value Input value.
     * @return string
     */
    public function sanitize_target_slug($value) {
        $value = strtolower(trim((string) $value));
        return $this->validated_setting(
            'otter_synca_target_slug',
            $value,
            array('Otter_Synca_Deployer', 'validate_target_slug')
        );
    }

    /**
     * Sanitize deployment type.
     *
     * @param mixed $value Input value.
     * @return string
     */
    public function sanitize_deploy_type($value) {
        $value = (string) $value;
        return in_array($value, array('plugin', 'theme'), true) ? $value : 'plugin';
    }

    /**
     * Auto deploy is available only while Pro is active.
     *
     * @param mixed $value Input value.
     * @return int
     */
    public function sanitize_auto_deploy($value) {
        return class_exists('Otter_Synca_Pro') && !empty($value) ? 1 : 0;
    }

    /**
     * Require a strong webhook secret when one is provided.
     *
     * @param mixed $value Input value.
     * @return string
     */
    public function sanitize_webhook_secret($value) {
        $value = trim(sanitize_text_field((string) $value));
        if ('' === $value || (strlen($value) >= 32 && strlen($value) <= 255)) {
            return Otter_Synca_Deployer::encrypt_secret($value);
        }

        add_settings_error(
            'otter_synca_webhook_secret',
            'otter_synca_invalid_webhook_secret',
            __('Webhook secret must contain between 32 and 255 characters.', 'otter-synca')
        );

        return Otter_Synca_Deployer::encrypt_secret(
            (string) get_option('otter_synca_webhook_secret', '')
        );
    }

    /**
     * Run a validator and retain the prior setting when validation fails.
     *
     * @param string   $option    Option name.
     * @param string   $value     Candidate value.
     * @param callable $validator Validator.
     * @return string
     */
    private function validated_setting($option, $value, $validator) {
        try {
            call_user_func($validator, $value);
            return $value;
        } catch (Exception $exception) {
            add_settings_error($option, 'otter_synca_invalid_' . $option, $exception->getMessage());
            return (string) get_option($option, '');
        }
    }

    /**
     * Encrypt credentials created by versions prior to 1.0.2.
     */
    private function migrate_credentials() {
        if (get_option('otter_synca_credentials_encrypted', false)) {
            return;
        }

        foreach (array('otter_synca_github_token', 'otter_synca_webhook_secret') as $option) {
            $value = (string) get_option($option, '');
            if ('' !== $value) {
                update_option($option, Otter_Synca_Deployer::encrypt_secret($value), false);
            }
        }

        update_option('otter_synca_credentials_encrypted', 1, false);
    }

    /**
     * Check whether the current screen belongs to OtterSynca.
     *
     * @return bool
     */
    private function is_plugin_screen() {
        $screen = get_current_screen();
        return $screen && in_array(
            $screen->id,
            array(
                'toplevel_page_otter-synca',
                'otter-synca_page_otter-synca-premium',
                'otter-synca_page_otter-synca-help',
                'otter-synca_page_otter-synca-coffee',
            ),
            true
        );
    }
}
