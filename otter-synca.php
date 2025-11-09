<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://ottersynca.com
 * @since             1.0.1
 * @package           Otter_Synca
 *
 * @wordpress-plugin
 * Plugin Name:       OtterSynca – Git Deployment & Sync Tool
 * Plugin URI:        https://ottersynca.com
 * Description:       Deploy WordPress plugins and themes directly from GitHub repositories. Automatize your workflow with automatic deploys, multiple repositories, backups, and detailed logs. Upgrade to <a href="https://plugins.cyfer.com.br/downloads/ottersynca-deploy-para-wordpress/" target="_blank">OtterSynca Pro</a> for advanced features. Plans starting at $69/year.
 * Version:           1.0.1
 * Author:            Cyfer Development
 * Author URI:        https://cyfer.com.br
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       otter-synca
 * Domain Path:       /languages
 * Requires at least: 5.0
 * Tested up to:      6.4
 * Requires PHP:      7.4
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('OTTER_SYNCA_VERSION', '1.0.1');
define('OTTER_SYNCA_PLUGIN_NAME', 'otter-synca');
define('OTTER_SYNCA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OTTER_SYNCA_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_otter_synca() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-otter-synca-activator.php';
    Otter_Synca_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_otter_synca() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-otter-synca-deactivator.php';
    Otter_Synca_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_otter_synca');
register_deactivation_hook(__FILE__, 'deactivate_otter_synca');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-otter-synca.php';

/**
 * Begins execution of the plugin.
 */
function run_otter_synca() {
    $plugin = new Otter_Synca();
    $plugin->run();
}
run_otter_synca(); 