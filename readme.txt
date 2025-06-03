=== OtterSynca - Git Deployment for WordPress ===
Contributors: ottersynca
Tags: deployment, git, github, deploy, sync
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Deploy WordPress plugins and themes directly from GitHub repositories.

== Description ==

OtterSynca is a powerful WordPress plugin that allows you to deploy your plugins and themes directly from GitHub repositories. With a simple and intuitive interface, you can easily keep your WordPress installations up to date with your latest code changes.

= Features =

* GitHub integration using personal access tokens
* Deploy plugins and themes from any GitHub repository
* Select specific branches for deployment
* Manual deployment with a single click
* Simple deployment logs
* Secure token storage
* Clean and intuitive admin interface

= Free Version Features =

* GitHub authentication via personal access token
* Repository selection
* Branch selection
* Manual deployment
* Basic deployment logs
* Plugin and theme deployment support

== Installation ==

1. Upload the `otter-synca` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the OtterSynca settings page
4. Enter your GitHub personal access token
5. Configure your repository settings
6. Start deploying!

== Frequently Asked Questions ==

= How do I create a GitHub personal access token? =

1. Go to your GitHub account settings
2. Navigate to "Developer settings" > "Personal access tokens"
3. Click "Generate new token"
4. Give it a name and select the necessary scopes (repo access)
5. Copy the generated token and paste it in the OtterSynca settings

= What permissions does the plugin need? =

The plugin needs write permissions to your WordPress plugins and themes directories to perform deployments.

= Is it safe to store my GitHub token? =

Yes, the token is stored securely in the WordPress options table and is never exposed in the frontend.

== Screenshots ==

1. Main settings page
2. Deployment section
3. Deployment logs

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release 