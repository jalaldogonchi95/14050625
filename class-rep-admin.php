<?php
defined('ABSPATH') || exit;

class REP_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'menu'));
    }

    public function menu() {
        add_menu_page(
            'تنظیمات املاک',
            'تنظیمات املاک',
            'manage_options',
            'rep-settings',
            array($this, 'page'),
            'dashicons-admin-home',
            26
        );
    }

    public function page() {
        ?>
        <div class="wrap">
            <h1>Real Estate Pro</h1>
            <p>نسخه پایه افزونه فعال است.</p>

            <h2>شورت‌کدها</h2>
            <ul>
                <li><code>[rep_auth]</code> ثبت‌نام و ورود</li>
                <li><code>[rep_submit_listing]</code> فرم ثبت آگهی</li>
                <li><code>[rep_dashboard]</code> پنل کاربری</li>
                <li><code>[rep_property_search]</code> جست‌وجوی املاک</li>
            </ul>
        </div>
        <?php
    }
}
