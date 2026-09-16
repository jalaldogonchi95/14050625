<?php
defined('ABSPATH') || exit;

class REP_Activator {

    public static function activate() {
        require_once REP_PATH . 'includes/Listings/class-rep-listing-post-type.php';
        require_once REP_PATH . 'includes/Listings/class-rep-listing-meta.php';

        $post_type = new REP_Listing_Post_Type();
        $post_type->register();

        $meta = new REP_Listing_Meta();
        $meta->register();

        if (!get_role('rep_agent')) {
            add_role(
                'rep_agent',
                'مشاور املاک',
                array(
                    'read' => true,
                    'upload_files' => true,
                )
            );
        }

        add_option('rep_version', REP_VERSION);

        flush_rewrite_rules();
    }

    public static function deactivate() {
        flush_rewrite_rules();
    }
}
