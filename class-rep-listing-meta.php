<?php
defined('ABSPATH') || exit;

class REP_Listing_Meta {

    private $fields = array(
        'price',
        'area',
        'rooms',
        'year_built',
        'deposit',
        'rent',
        'floor',
        'parking',
        'elevator',
        'warehouse',
        'mobile',
    );

    public function __construct() {
        add_action('init', array($this, 'register'));
    }

    public function register() {
        foreach ($this->fields as $field) {
            register_post_meta('rep_listing', '_rep_' . $field, array(
                'type' => 'string',
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ));
        }
    }
}
