<?php
/**
 * Webhook handler for GitHub integration
 *
 * @package    Otter_Synca
 * @subpackage Otter_Synca/includes
 */

if (!defined('ABSPATH')) {
    exit;
}

class Otter_Synca_Webhook {

    /**
     * Initialize the webhook handler
     */
    public function __construct() {
        // Only register webhook if auto-deploy is enabled
        if (get_option('otter_synca_auto_deploy', false)) {
            add_action('init', array($this, 'register_webhook_endpoint'));
            add_action('template_redirect', array($this, 'handle_webhook_request'));
        }
    }

    /**
     * Register the webhook endpoint
     */
    public function register_webhook_endpoint() {
        add_rewrite_rule(
            '^otter-synca-webhook/?$',
            'index.php?otter_synca_webhook=1',
            'top'
        );
    }

    /**
     * Add query vars for the webhook
     */
    public function add_query_vars($vars) {
        $vars[] = 'otter_synca_webhook';
        return $vars;
    }

    /**
     * Handle webhook requests
     */
    public function handle_webhook_request() {
        // Check if auto-deploy is enabled before processing any webhook
        if (!get_option('otter_synca_auto_deploy', false)) {
            error_log("OtterSynca: Webhook endpoint accessed but auto-deploy is disabled");
            http_response_code(404);
            echo 'Webhook endpoint not available - auto-deploy is disabled';
            exit;
        }

        if (get_query_var('otter_synca_webhook')) {
            $this->process_webhook();
            exit;
        }
    }

    /**
     * Process the webhook payload
     */
    private function process_webhook() {
        // Double-check if auto-deploy is enabled (in case the setting was changed after webhook registration)
        if (!get_option('otter_synca_auto_deploy', false)) {
            error_log("OtterSynca: Webhook received but auto-deploy is disabled");
            http_response_code(200);
            echo 'Auto-deploy is disabled';
            return;
        }

        // Get the raw POST data
        $payload = file_get_contents('php://input');
        $headers = getallheaders();

        // Log webhook request for debugging
        error_log('OtterSynca: Webhook received - Headers: ' . print_r($headers, true));
        error_log('OtterSynca: Webhook payload: ' . $payload);

        // Verify GitHub webhook signature (if secret is configured)
        $webhook_secret = get_option('otter_synca_webhook_secret');
        if (!empty($webhook_secret)) {
            if (!$this->verify_webhook_signature($payload, $headers, $webhook_secret)) {
                http_response_code(401);
                echo 'Invalid signature';
                return;
            }
        }

        // Parse the JSON payload
        $data = json_decode($payload, true);
        if (!$data) {
            http_response_code(400);
            echo 'Invalid JSON payload';
            return;
        }

        // Check if this is a push event
        if (!isset($data['ref']) || !isset($data['repository'])) {
            http_response_code(400);
            echo 'Invalid webhook payload';
            return;
        }

        // Extract repository and branch information
        $repository = $data['repository']['full_name'];
        $ref = $data['ref'];
        $branch = str_replace('refs/heads/', '', $ref);
        $commit_sha = $data['after'];
        $commit_message = isset($data['head_commit']['message']) ? $data['head_commit']['message'] : '';

        // Log the webhook details
        error_log("OtterSynca: Webhook details - Repository: {$repository}, Branch: {$branch}, Commit: {$commit_sha}");

        // Check if this repository matches our configuration
        $configured_repository = get_option('otter_synca_repository');
        $configured_branch = get_option('otter_synca_branch', 'main');

        if ($repository !== $configured_repository) {
            error_log("OtterSynca: Repository mismatch - Expected: {$configured_repository}, Received: {$repository}");
            http_response_code(200);
            echo 'Repository not configured for this webhook';
            return;
        }

        if ($branch !== $configured_branch) {
            error_log("OtterSynca: Branch mismatch - Expected: {$configured_branch}, Received: {$branch}");
            http_response_code(200);
            echo 'Branch not configured for auto-deploy';
            return;
        }

        // Check if auto-deploy is enabled
        $auto_deploy_enabled = get_option('otter_synca_auto_deploy', false);
        if (!$auto_deploy_enabled) {
            error_log("OtterSynca: Auto-deploy is disabled");
            http_response_code(200);
            echo 'Auto-deploy is disabled';
            return;
        }

        // Check if we should skip this commit (e.g., if it's a merge commit or specific pattern)
        if ($this->should_skip_commit($commit_message)) {
            error_log("OtterSynca: Skipping commit due to message pattern: {$commit_message}");
            http_response_code(200);
            echo 'Commit skipped based on message pattern';
            return;
        }

        // Check if we've already deployed this commit
        $last_deployed_commit = get_option('otter_synca_last_deployed_commit');
        if ($last_deployed_commit === $commit_sha) {
            error_log("OtterSynca: Commit already deployed: {$commit_sha}");
            http_response_code(200);
            echo 'Commit already deployed';
            return;
        }

        // Check if there's another deploy in progress
        $deploy_lock = get_transient('otter_synca_deploy_lock');
        if ($deploy_lock) {
            error_log("OtterSynca: Deploy already in progress");
            http_response_code(200);
            echo 'Deploy already in progress';
            return;
        }

        // Set deploy lock (5 minutes)
        set_transient('otter_synca_deploy_lock', true, 300);

        try {
            // Perform the deployment
            $result = $this->perform_deployment($repository, $branch, $commit_sha, $commit_message);
            
            if ($result['success']) {
                // Update last deployed commit
                update_option('otter_synca_last_deployed_commit', $commit_sha);
                
                // Update last deploy info
                update_option('otter_synca_last_deploy', array(
                    'status' => 'success',
                    'message' => 'Auto-deploy completed successfully from webhook',
                    'timestamp' => current_time('mysql'),
                    'commit_sha' => $commit_sha,
                    'commit_message' => $commit_message,
                    'branch' => $branch
                ));

                error_log("OtterSynca: Auto-deploy completed successfully for commit {$commit_sha}");
                http_response_code(200);
                echo 'Deploy completed successfully';
            } else {
                throw new Exception($result['message']);
            }
        } catch (Exception $e) {
            error_log("OtterSynca: Auto-deploy failed: " . $e->getMessage());
            
            // Update last deploy info with error
            update_option('otter_synca_last_deploy', array(
                'status' => 'error',
                'message' => 'Auto-deploy failed: ' . $e->getMessage(),
                'timestamp' => current_time('mysql'),
                'commit_sha' => $commit_sha,
                'commit_message' => $commit_message,
                'branch' => $branch
            ));

            http_response_code(500);
            echo 'Deploy failed: ' . $e->getMessage();
        } finally {
            // Remove deploy lock
            delete_transient('otter_synca_deploy_lock');
        }
    }

    /**
     * Verify GitHub webhook signature
     */
    private function verify_webhook_signature($payload, $headers, $secret) {
        if (!isset($headers['X-Hub-Signature-256'])) {
            return false;
        }

        $signature = $headers['X-Hub-Signature-256'];
        $expected_signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        
        return hash_equals($expected_signature, $signature);
    }

    /**
     * Check if commit should be skipped
     */
    private function should_skip_commit($commit_message) {
        // Skip merge commits
        if (strpos($commit_message, 'Merge') === 0) {
            return true;
        }

        // Skip commits with [skip-deploy] in message
        if (strpos($commit_message, '[skip-deploy]') !== false) {
            return true;
        }

        // Skip commits with [no-deploy] in message
        if (strpos($commit_message, '[no-deploy]') !== false) {
            return true;
        }

        return false;
    }

    /**
     * Perform the actual deployment
     */
    private function perform_deployment($repository, $branch, $commit_sha, $commit_message) {
        $github_token = get_option('otter_synca_github_token');
        $deploy_type = get_option('otter_synca_deploy_type', 'plugin');
        $target_slug = get_option('otter_synca_target_slug');

        if (empty($github_token) || empty($target_slug)) {
            throw new Exception('GitHub token or target slug not configured');
        }

        // Download repository ZIP using the commit SHA
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
            throw new Exception('Failed to download repository');
        }

        // Extract ZIP file
        $temp_dir = get_temp_dir();
        $zip_file = $temp_dir . 'otter-synca-webhook-temp.zip';
        file_put_contents($zip_file, $zip_content);

        $zip = new ZipArchive;
        if ($zip->open($zip_file) !== TRUE) {
            throw new Exception('Failed to open ZIP file');
        }

        $extract_path = $temp_dir . 'otter-synca-webhook-extract/';
        $zip->extractTo($extract_path);
        $zip->close();

        // Get the first directory (GitHub adds a hash to the directory name)
        $extracted_dirs = glob($extract_path . '*', GLOB_ONLYDIR);
        if (empty($extracted_dirs)) {
            throw new Exception('Failed to extract repository');
        }
        $source_dir = $extracted_dirs[0];

        // Determine target directory
        $target_dir = $deploy_type === 'plugin' 
            ? WP_PLUGIN_DIR . '/' . $target_slug
            : WP_CONTENT_DIR . '/themes/' . $target_slug;

        // Copy files
        $this->copy_directory($source_dir, $target_dir);

        // Clean up
        wp_delete_file($zip_file);
        $this->remove_directory($extract_path);

        return array(
            'success' => true,
            'message' => 'Deployment completed successfully'
        );
    }

    /**
     * Copy a directory recursively
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
     * Remove a directory recursively
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
     * Get webhook URL
     */
    public static function get_webhook_url() {
        return home_url('/otter-synca-webhook/');
    }
} 