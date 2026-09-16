<?php
defined('ABSPATH') || exit;

class REP_Listing_Post_Type {

    public function __construct() {
        add_action('init', array($this, 'register'));
    }

    public function register() {

        register_post_type('rep_listing', array(
            'labels' => array(
                'name' => 'آگهی‌های املاک',
                'singular_name' => 'آگهی ملک',
                'add_new' => 'افزودن آگهی',
                'add_new_item' => 'افزودن آگهی جدید',
                'edit_item' => 'ویرایش آگهی',
                'all_items' => 'همه آگهی‌ها',
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'property'),
            'supports' => array('title', 'editor', 'author', 'thumbnail'),
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-building',
        ));

        register_taxonomy('rep_city', 'rep_listing', array(
            'labels' => array(
                'name' => 'شهرها',
                'singular_name' => 'شهر',
            ),
            'public' => true,
            'hierarchical' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'city'),
        ));

        register_taxonomy('rep_region', 'rep_listing', array(
            'labels' => array(
                'name' => 'مناطق',
                'singular_name' => 'منطقه',
            ),
            'public' => true,
            'hierarchical' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'region'),
        ));

        register_taxonomy('rep_property_type', 'rep_listing', array(
            'labels' => array(
                'name' => 'نوع ملک',
                'singular_name' => 'نوع ملک',
            ),
            'public' => true,
            'hierarchical' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ));

        register_taxonomy('rep_transaction_type', 'rep_listing', array(
            'labels' => array(
                'name' => 'نوع معامله',
                'singular_name' => 'نوع معامله',
            ),
            'public' => true,
            'hierarchical' => false,
            'show_admin_column' => true,
            'show_in_rest' => true,
        ));
    }
}
