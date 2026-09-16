<?php
defined('ABSPATH') || exit;

class REP_Listing_Form {

    public function __construct() {
        add_shortcode('rep_submit_listing', array($this, 'render'));
        add_action('admin_post_rep_submit_listing', array($this, 'submit'));
    }

    public function render() {

        if (!is_user_logged_in()) {
            return '<div class="rep-notice">برای ثبت آگهی ابتدا باید وارد حساب کاربری شوید.</div>';
        }

        $cities = get_terms(array(
            'taxonomy' => 'rep_city',
            'hide_empty' => false,
        ));

        $regions = get_terms(array(
            'taxonomy' => 'rep_region',
            'hide_empty' => false,
        ));

        $types = get_terms(array(
            'taxonomy' => 'rep_property_type',
            'hide_empty' => false,
        ));

        $transactions = get_terms(array(
            'taxonomy' => 'rep_transaction_type',
            'hide_empty' => false,
        ));

        ob_start();
        ?>
        <form class="rep-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="rep_submit_listing">
            <?php wp_nonce_field('rep_submit_listing', 'rep_nonce'); ?>

            <h2>ثبت آگهی ملک</h2>

            <label>عنوان آگهی *</label>
            <input type="text" name="title" required>

            <label>توضیحات</label>
            <textarea name="description" rows="6"></textarea>

            <div class="rep-grid">
                <div>
                    <label>قیمت کل</label>
                    <input type="number" name="price" min="0">
                </div>
                <div>
                    <label>متراژ</label>
                    <input type="number" name="area" min="0">
                </div>
                <div>
                    <label>تعداد اتاق</label>
                    <input type="number" name="rooms" min="0">
                </div>
                <div>
                    <label>سال ساخت</label>
                    <input type="number" name="year_built" min="1300">
                </div>
                <div>
                    <label>ودیعه</label>
                    <input type="number" name="deposit" min="0">
                </div>
                <div>
                    <label>اجاره ماهانه</label>
                    <input type="number" name="rent" min="0">
                </div>
            </div>

            <div class="rep-grid">
                <div>
                    <label>شهر</label>
                    <select name="city">
                        <option value="">انتخاب کنید</option>
                        <?php foreach ($cities as $term) : ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>">
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label>منطقه</label>
                    <select name="region">
                        <option value="">انتخاب کنید</option>
                        <?php foreach ($regions as $term) : ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>">
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label>نوع ملک</label>
                    <select name="property_type">
                        <option value="">انتخاب کنید</option>
                        <?php foreach ($types as $term) : ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>">
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label>نوع معامله</label>
                    <select name="transaction_type">
                        <option value="">انتخاب کنید</option>
                        <?php foreach ($transactions as $term) : ?>
                            <option value="<?php echo esc_attr($term->term_id); ?>">
                                <?php echo esc_html($term->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <label>شماره تماس</label>
            <input type="tel" name="mobile">

            <label>تصاویر ملک</label>
            <input type="file" name="listing_images[]" multiple accept="image/jpeg,image/png,image/webp">

            <button type="submit">ثبت آگهی</button>
        </form>
        <?php
        return ob_get_clean();
    }

    public function submit() {

        if (
            !is_user_logged_in() ||
            !isset($_POST['rep_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['rep_nonce'])),
                'rep_submit_listing'
            )
        ) {
            wp_die('دسترسی نامعتبر است.');
        }

        $title = isset($_POST['title']) ? sanitize_text_field(wp_unslash($_POST['title'])) : '';
        $description = isset($_POST['description']) ? wp_kses_post(wp_unslash($_POST['description'])) : '';

        if (empty($title)) {
            wp_die('عنوان آگهی الزامی است.');
        }

        $post_id = wp_insert_post(array(
            'post_type' => 'rep_listing',
            'post_status' => 'pending',
            'post_title' => $title,
            'post_content' => $description,
            'post_author' => get_current_user_id(),
        ), true);

        if (is_wp_error($post_id)) {
            wp_die('خطا در ثبت آگهی.');
        }

        $numeric_fields = array(
            'price',
            'area',
            'rooms',
            'year_built',
            'deposit',
            'rent',
        );

        foreach ($numeric_fields as $field) {
            if (isset($_POST[$field]) && $_POST[$field] !== '') {
                update_post_meta(
                    $post_id,
                    '_rep_' . $field,
                    absint($_POST[$field])
                );
            }
        }

        if (!empty($_POST['mobile'])) {
            update_post_meta(
                $post_id,
                '_rep_mobile',
                sanitize_text_field(wp_unslash($_POST['mobile']))
            );
        }

        $taxonomies = array(
            'city' => 'rep_city',
            'region' => 'rep_region',
            'property_type' => 'rep_property_type',
            'transaction_type' => 'rep_transaction_type',
        );

        foreach ($taxonomies as $field => $taxonomy) {
            if (!empty($_POST[$field])) {
                wp_set_object_terms(
                    $post_id,
                    absint($_POST[$field]),
                    $taxonomy
                );
            }
        }

        if (!empty($_FILES['listing_images'])) {
            $images = new REP_Listing_Images();
            $images->handle($post_id, $_FILES['listing_images']);
        }

        wp_safe_redirect(add_query_arg(
            'rep_listing_submitted',
            '1',
            wp_get_referer() ? wp_get_referer() : home_url('/')
        ));
        exit;
    }
}
