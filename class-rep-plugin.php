<?php
defined('ABSPATH') || exit;

class REP_Plugin {

    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->includes();
        $this->init();
    }

    private function includes() {
        require_once REP_PATH . 'includes/Listings/class-rep-listing-post-type.php';
        require_once REP_PATH . 'includes/Listings/class-rep-listing-meta.php';
        require_once REP_PATH . 'includes/Listings/class-rep-listing-form.php';
        require_once REP_PATH . 'includes/Listings/class-rep-listing-images.php';

        require_once REP_PATH . 'includes/Auth/class-rep-auth.php';
        require_once REP_PATH . 'includes/Search/class-rep-search.php';
        require_once REP_PATH . 'includes/Admin/class-rep-admin.php';
    }

    private function init() {
        new REP_Listing_Post_Type();
        new REP_Listing_Meta();
        new REP_Listing_Form();
        new REP_Listing_Images();
        new REP_Auth();
        new REP_Search();
        new REP_Admin();

        add_action('wp_enqueue_scripts', array($this, 'assets'));
    }

    public function assets() {
        wp_enqueue_style(
            'rep-public',
            REP_URL . 'assets/css/public.css',
            array(),
            REP_VERSION
        );
    }
}
