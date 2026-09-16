<?php
defined('ABSPATH') || exit;

class REP_Listing_Images {

    public function handle($post_id, $files) {

        if (empty($files['name'][0])) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_ids = array();

        foreach ($files['name'] as $index => $name) {
            if (empty($name)) {
                continue;
            }

            $_FILES['rep_single_image'] = array(
                'name' => sanitize_file_name($files['name'][$index]),
                'type' => $files['type'][$index],
                'tmp_name' => $files['tmp_name'][$index],
                'error' => $files['error'][$index],
                'size' => $files['size'][$index],
            );

            if (0 !== (int) $_FILES['rep_single_image']['error']) {
                continue;
            }

            $attachment_id = media_handle_upload(
                'rep_single_image',
                $post_id
            );

            if (!is_wp_error($attachment_id)) {
                $attachment_ids[] = (int) $attachment_id;
            }
        }

        if (!empty($attachment_ids)) {
            set_post_thumbnail($post_id, $attachment_ids[0]);
            update_post_meta($post_id, '_rep_gallery', $attachment_ids);
        }
    }
}
