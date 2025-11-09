<?php

/**
 * The core plugin class.
 *
 * @since      1.0.1
 * @package    Otter_Synca
 * @subpackage Otter_Synca/includes
 */
class Otter_Synca {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.1
     * @access   protected
     * @var      Otter_Synca_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.1
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.1
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.1
     */
    public function __construct() {
        if (defined('OTTER_SYNCA_VERSION')) {
            $this->version = OTTER_SYNCA_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'otter-synca';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.1
     * @access   private
     */
    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-otter-synca-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-otter-synca-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-otter-synca-webhook.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-otter-synca-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-otter-synca-public.php';

        $this->loader = new Otter_Synca_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     * WordPress automatically loads translations since version 4.6.
     *
     * @since    1.0.1
     * @access   private
     */
    private function set_locale() {
        // WordPress automatically loads translations for plugins
        // No manual action needed since WordPress 4.6
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Otter_Synca_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        
        // Initialize webhook handler only if auto-deploy is enabled
        if (get_option('otter_synca_auto_deploy', false)) {
            new Otter_Synca_Webhook();
            // Add query vars for webhook
            $this->loader->add_filter('query_vars', $this, 'add_webhook_query_vars');
        }
        
        // Add hook to handle auto-deploy setting changes
        $this->loader->add_action('update_option_otter_synca_auto_deploy', $this, 'handle_auto_deploy_setting_change', 10, 3);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.1
     * @access   private
     */
    private function define_public_hooks() {
        // OtterSynca é um plugin apenas para admin, não precisa de hooks públicos
        // $plugin_public = new Otter_Synca_Public($this->get_plugin_name(), $this->get_version());

        // $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        // $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.1
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.1
     * @return    Otter_Synca_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.1
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Add query vars for webhook endpoint
     *
     * @since     1.0.1
     * @param     array    $vars    Existing query vars
     * @return    array    Modified query vars
     */
    public function add_webhook_query_vars($vars) {
        $vars[] = 'otter_synca_webhook';
        return $vars;
    }

    /**
     * Handle auto-deploy setting changes
     *
     * @since     1.0.0
     * @param     string    $option    The option name
     * @param     mixed     $old_value The old option value
     * @param     mixed     $value     The new option value
     */
    public function handle_auto_deploy_setting_change($option, $old_value, $value) {
        // Flush rewrite rules when auto-deploy setting changes
        // This ensures the webhook endpoint is properly registered/unregistered
        flush_rewrite_rules();
        
        // Log the change
        error_log("OtterSynca: Auto-deploy setting changed from " . ($old_value ? 'enabled' : 'disabled') . " to " . ($value ? 'enabled' : 'disabled'));
    }
} 