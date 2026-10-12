<?php
/**
 * Secure GitHub webhook handler.
 *
 * @package    Otter_Synca
 * @subpackage Otter_Synca/includes
 */

if (!defined('ABSPATH')) {
    exit;
}

class Otter_Synca_Webhook {

    const MAX_PAYLOAD_SIZE = 1048576;

    /**
     * Register webhook routing.
     */
    public function __construct() {
        add_action('init', array($this, 'register_webhook_endpoint'));
        add_action('template_redirect', array($this, 'handle_webhook_request'));
    }

    /**
     * Register the public endpoint.
     */
    public function register_webhook_endpoint() {
        add_rewrite_rule(
            '^otter-synca-webhook/?$',
            'index.php?otter_synca_webhook=1',
            'top'
        );
    }

    /**
     * Handle requests routed to the webhook endpoint.
     */
    public function handle_webhook_request() {
        if (!get_query_var('otter_synca_webhook')) {
            return;
        }

        if (
            !class_exists('Otter_Synca_Pro')
            || !get_option('otter_synca_auto_deploy', false)
        ) {
            $this->respond(404, __('Webhook endpoint is unavailable.', 'otter-synca'));
        }

        if ('POST' !== strtoupper(isset($_SERVER['REQUEST_METHOD']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) : '')) {
            $this->respond(405, __('Method not allowed.', 'otter-synca'));
        }

        $this->process_webhook();
    }

    /**
     * Authenticate and process a GitHub push event.
     */
    private function process_webhook() {
        $secret = (string) get_option('otter_synca_webhook_secret', '');
        if (strlen($secret) < 32) {
            $this->respond(503, __('Webhook is not securely configured.', 'otter-synca'));
        }

        $event = isset($_SERVER['HTTP_X_GITHUB_EVENT'])
            ? sanitize_text_field(wp_unslash($_SERVER['HTTP_X_GITHUB_EVENT']))
            : '';
        if ('push' !== $event) {
            $this->respond(202, __('Event ignored.', 'otter-synca'));
        }

        $content_length = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
        if ($content_length > self::MAX_PAYLOAD_SIZE) {
            $this->respond(413, __('Webhook payload is too large.', 'otter-synca'));
        }

        $payload = file_get_contents('php://input', false, null, 0, self::MAX_PAYLOAD_SIZE + 1);
        if (!is_string($payload) || '' === $payload || strlen($payload) > self::MAX_PAYLOAD_SIZE) {
            $this->respond(400, __('Invalid webhook payload.', 'otter-synca'));
        }

        $signature = isset($_SERVER['HTTP_X_HUB_SIGNATURE_256'])
            ? sanitize_text_field(wp_unslash($_SERVER['HTTP_X_HUB_SIGNATURE_256']))
            : '';
        if (!$this->verify_webhook_signature($payload, $signature, $secret)) {
            $this->respond(401, __('Invalid webhook signature.', 'otter-synca'));
        }

        $data = json_decode($payload, true);
        if (!is_array($data) || JSON_ERROR_NONE !== json_last_error()) {
            $this->respond(400, __('Invalid JSON payload.', 'otter-synca'));
        }

        $repository = isset($data['repository']['full_name']) ? (string) $data['repository']['full_name'] : '';
        $ref = isset($data['ref']) ? (string) $data['ref'] : '';
        $commit_sha = isset($data['after']) ? strtolower((string) $data['after']) : '';

        if (0 !== strpos($ref, 'refs/heads/')) {
            $this->respond(202, __('Non-branch event ignored.', 'otter-synca'));
        }

        $branch = substr($ref, strlen('refs/heads/'));
        try {
            Otter_Synca_Deployer::validate_repository($repository);
            Otter_Synca_Deployer::validate_branch($branch);
        } catch (Exception $exception) {
            $this->respond(400, __('Invalid webhook repository or branch.', 'otter-synca'));
        }

        if (!preg_match('/^[a-f0-9]{40}$/', $commit_sha)) {
            $this->respond(400, __('Invalid webhook commit identifier.', 'otter-synca'));
        }

        $configured_repository = (string) get_option('otter_synca_repository', '');
        $configured_branch = (string) get_option('otter_synca_branch', 'main');
        if (!hash_equals($configured_repository, $repository) || !hash_equals($configured_branch, $branch)) {
            $this->respond(202, __('Repository or branch is not configured for this webhook.', 'otter-synca'));
        }

        if ((string) get_option('otter_synca_last_deployed_commit', '') === $commit_sha) {
            $this->respond(200, __('Commit was already deployed.', 'otter-synca'));
        }

        $commit_message = isset($data['head_commit']['message'])
            ? wp_html_excerpt(sanitize_textarea_field((string) $data['head_commit']['message']), 500, '')
            : '';

        if ($this->should_skip_commit($commit_message)) {
            $this->respond(202, __('Commit skipped by its message.', 'otter-synca'));
        }

        try {
            Otter_Synca_Deployer::deploy(
                $repository,
                $commit_sha,
                (string) get_option('otter_synca_github_token', ''),
                (string) get_option('otter_synca_deploy_type', 'plugin'),
                (string) get_option('otter_synca_target_slug', '')
            );

            update_option('otter_synca_last_deployed_commit', $commit_sha);
            update_option('otter_synca_last_deploy', array(
                'status'         => 'success',
                'message'        => __('Automatic deploy completed successfully.', 'otter-synca'),
                'timestamp'      => current_time('mysql'),
                'commit_sha'     => $commit_sha,
                'commit_message' => $commit_message,
                'branch'         => $branch,
            ));

            $this->respond(200, __('Deploy completed successfully.', 'otter-synca'));
        } catch (Exception $exception) {
            update_option('otter_synca_last_deploy', array(
                'status'     => 'error',
                'message'    => $exception->getMessage(),
                'timestamp'  => current_time('mysql'),
                'commit_sha' => $commit_sha,
                'branch'     => $branch,
            ));

            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('OtterSynca webhook deploy failed: ' . $exception->getMessage());
            }

            $this->respond(500, __('Deploy failed. Review the OtterSynca status in WordPress.', 'otter-synca'));
        }
    }

    /**
     * Validate an HMAC SHA-256 signature.
     *
     * @param string $payload   Raw request payload.
     * @param string $signature Signature header.
     * @param string $secret    Configured secret.
     * @return bool
     */
    private function verify_webhook_signature($payload, $signature, $secret) {
        if (!preg_match('/^sha256=[a-f0-9]{64}$/i', $signature)) {
            return false;
        }

        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, strtolower($signature));
    }

    /**
     * Determine whether deployment was disabled in the commit message.
     *
     * @param string $message Commit message.
     * @return bool
     */
    private function should_skip_commit($message) {
        return false !== stripos($message, '[skip-deploy]')
            || false !== stripos($message, '[no-deploy]');
    }

    /**
     * Send a plain-text webhook response and stop execution.
     *
     * @param int    $status  HTTP status.
     * @param string $message Response message.
     */
    private function respond($status, $message) {
        status_header($status);
        nocache_headers();
        header('Content-Type: text/plain; charset=' . get_option('blog_charset', 'UTF-8'));
        echo esc_html($message);
        exit;
    }

    /**
     * Public webhook URL.
     *
     * @return string
     */
    public static function get_webhook_url() {
        return home_url('/otter-synca-webhook/');
    }
}
