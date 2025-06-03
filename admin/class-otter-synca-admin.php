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
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/otter-synca-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/otter-synca-admin.js', array('jquery'), $this->version, false);
        
        // Add localized script data
        wp_localize_script($this->plugin_name, 'otter_synca', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('otter_synca_nonce'),
            'deploying' => __('Deploying...', 'otter-synca'),
            'success' => __('Deploy successful!', 'otter-synca'),
            'error' => __('Deploy failed!', 'otter-synca')
        ));
    }

    /**
     * Add menu items to the admin area.
     *
     * @since    1.0.0
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
     * Register plugin settings.
     *
     * @since    1.0.0
     */
    public function register_settings() {
        register_setting($this->plugin_name, 'otter_synca_github_token');
        register_setting($this->plugin_name, 'otter_synca_repository');
        register_setting($this->plugin_name, 'otter_synca_branch');
        register_setting($this->plugin_name, 'otter_synca_deploy_type');
        register_setting($this->plugin_name, 'otter_synca_target_slug');
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_admin_page() {
        include_once 'partials/otter-synca-admin-display.php';
    }

    /**
     * Handle the deploy action via AJAX.
     *
     * @since    1.0.0
     */
    public function handle_deploy() {
        check_ajax_referer('otter_synca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You do not have sufficient permissions to access this page.', 'otter-synca'));
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
            // Download repository ZIP
            $zip_url = "https://api.github.com/repos/{$repository}/zipball/{$branch}";
            $response = wp_remote_get($zip_url, array(
                'headers' => array(
                    'Authorization' => 'token ' . $github_token,
                    'Accept' => 'application/vnd.github.v3+json'
                )
            ));

            if (is_wp_error($response)) {
                throw new Exception($response->get_error_message());
            }

            $zip_content = wp_remote_retrieve_body($response);
            if (empty($zip_content)) {
                throw new Exception(__('Failed to download repository.', 'otter-synca'));
            }

            // Save ZIP file temporarily
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
                    throw new Exception(__('Failed to extract repository.', 'otter-synca'));
                }
                $source_dir = $extracted_dirs[0];

                // Determine target directory
                $target_dir = $deploy_type === 'plugin' 
                    ? WP_PLUGIN_DIR . '/' . $target_slug
                    : WP_CONTENT_DIR . '/themes/' . $target_slug;

                // Copy files
                $this->copy_directory($source_dir, $target_dir);

                // Clean up
                unlink($zip_file);
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
            is_dir($path) ? $this->remove_directory($path) : unlink($path);
        }
        return rmdir($dir);
    }
} 