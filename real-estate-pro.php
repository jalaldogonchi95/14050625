<?php
/**
 * Plugin Name: Real Estate Pro
 * Description: سیستم پایه مدیریت آگهی املاک با پنل کاربری، ثبت آگهی و جست‌وجوی فیلترشده.
 * Version: 0.1.0
 * Author: Custom Development
 * Text Domain: real-estate-pro
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

defined('ABSPATH') || exit;

define('REP_VERSION', '0.1.0');
define('REP_FILE', __FILE__);
define('REP_PATH', plugin_dir_path(__FILE__));
define('REP_URL', plugin_dir_url(__FILE__));

require_once REP_PATH . 'includes/class-rep-activator.php';
require_once REP_PATH . 'includes/class-rep-plugin.php';

register_activation_hook(__FILE__, array('REP_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('REP_Activator', 'deactivate'));

function rep_bootstrap() {
    return REP_Plugin::instance();
}

add_action('plugins_loaded', 'rep_bootstrap');
