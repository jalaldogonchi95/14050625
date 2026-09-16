<?php
defined('ABSPATH') || exit;

class REP_Auth {

    public function __construct() {
        add_shortcode('rep_auth', array($this, 'render_auth'));
        add_shortcode('rep_dashboard', array($this, 'dashboard'));

        add_action('admin_post_nopriv_rep_register', array($this, 'register'));
        add_action('admin_post_rep_register', array($this, 'register'));
    }

    public function render_auth() {

        if (is_user_logged_in()) {
            return '<div class="rep-notice">شما وارد حساب کاربری شده‌اید.</div>';
        }

        ob_start();
        ?>
        <div class="rep-auth">
            <form class="rep-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="rep_register">
                <?php wp_nonce_field('rep_register', 'rep_register_nonce'); ?>

                <h2>ثبت‌نام</h2>

                <label>نام کاربری *</label>
                <input type="text" name="username" required>

                <label>ایمیل *</label>
                <input type="email" name="email" required>

                <label>شماره موبایل</label>
                <input type="tel" name="mobile">

                <label>رمز عبور *</label>
                <input type="password" name="password" required>

                <button type="submit">ثبت‌نام</button>
            </form>

            <form class="rep-form" method="post" action="<?php echo esc_url(wp_login_url(get_permalink())); ?>">
                <h2>ورود</h2>

                <label>نام کاربری یا ایمیل</label>
                <input type="text" name="log" required>

                <label>رمز عبور</label>
                <input type="password" name="pwd" required>

                <input type="hidden" name="redirect_to" value="<?php echo esc_url(get_permalink()); ?>">

                <button type="submit">ورود</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function register() {

        if (
            !isset($_POST['rep_register_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['rep_register_nonce'])),
                'rep_register'
            )
        ) {
            wp_die('درخواست نامعتبر است.');
        }

        $username = sanitize_user(wp_unslash($_POST['username']));
        $email = sanitize_email(wp_unslash($_POST['email']));
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $mobile = isset($_POST['mobile']) ? sanitize_text_field(wp_unslash($_POST['mobile'])) : '';

        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            wp_die($user_id->get_error_message());
        }

        update_user_meta($user_id, 'rep_mobile', $mobile);

        $user = new WP_User($user_id);
        $user->set_role('subscriber');

        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        wp_safe_redirect(wp_get_referer() ? wp_get_referer() : home_url('/'));
        exit;
    }

    public function dashboard() {

        if (!is_user_logged_in()) {
            return '<div class="rep-notice">برای مشاهده پنل ابتدا وارد شوید.</div>';
        }

        $query = new WP_Query(array(
            'post_type' => 'rep_listing',
            'author' => get_current_user_id(),
            'post_status' => array('publish', 'pending', 'draft'),
            'posts_per_page' => -1,
        ));

        ob_start();
        ?>
        <div class="rep-dashboard">
            <h2>پنل کاربری من</h2>

            <p>
                <a class="rep-button" href="<?php echo esc_url(home_url('/')); ?>">
                    ثبت آگهی جدید
                </a>
            </p>

            <h3>آگهی‌های من</h3>

            <?php if ($query->have_posts()) : ?>
                <ul class="rep-listings">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <li>
                            <strong><?php the_title(); ?></strong>
                            <span>وضعیت: <?php echo esc_html(get_post_status_object(get_post_status())->label); ?></span>
                        </li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>
            <?php else : ?>
                <p>هنوز آگهی ثبت نکرده‌اید.</p>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
