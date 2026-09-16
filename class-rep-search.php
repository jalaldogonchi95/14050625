<?php
defined('ABSPATH') || exit;

class REP_Search {

    public function __construct() {
        add_shortcode('rep_property_search', array($this, 'render'));
    }

    public function render() {

        $cities = get_terms(array(
            'taxonomy' => 'rep_city',
            'hide_empty' => false,
        ));

        ob_start();
        ?>
        <div class="rep-search-wrap">
            <form class="rep-form rep-search" method="get">

                <div class="rep-grid">
                    <div>
                        <label>شهر</label>
                        <select name="rep_city">
                            <option value="">همه شهرها</option>
                            <?php foreach ($cities as $city) : ?>
                                <option value="<?php echo esc_attr($city->term_id); ?>">
                                    <?php echo esc_html($city->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>حداقل قیمت</label>
                        <input type="number" name="rep_min_price">
                    </div>

                    <div>
                        <label>حداکثر قیمت</label>
                        <input type="number" name="rep_max_price">
                    </div>

                    <div>
                        <label>حداقل متراژ</label>
                        <input type="number" name="rep_min_area">
                    </div>

                    <div>
                        <label>حداکثر متراژ</label>
                        <input type="number" name="rep_max_area">
                    </div>
                </div>

                <button type="submit">جست‌وجو</button>
            </form>

            <?php
            if (
                isset($_GET['rep_min_price']) ||
                isset($_GET['rep_max_price']) ||
                isset($_GET['rep_city'])
            ) {
                $this->results();
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    private function results() {

        $args = array(
            'post_type' => 'rep_listing',
            'post_status' => 'publish',
            'posts_per_page' => 20,
            'meta_query' => array('relation' => 'AND'),
        );

        if (!empty($_GET['rep_min_price'])) {
            $args['meta_query'][] = array(
                'key' => '_rep_price',
                'value' => absint($_GET['rep_min_price']),
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }

        if (!empty($_GET['rep_max_price'])) {
            $args['meta_query'][] = array(
                'key' => '_rep_price',
                'value' => absint($_GET['rep_max_price']),
                'compare' => '<=',
                'type' => 'NUMERIC',
            );
        }

        if (!empty($_GET['rep_min_area'])) {
            $args['meta_query'][] = array(
                'key' => '_rep_area',
                'value' => absint($_GET['rep_min_area']),
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }

        if (!empty($_GET['rep_max_area'])) {
            $args['meta_query'][] = array(
                'key' => '_rep_area',
                'value' => absint($_GET['rep_max_area']),
                'compare' => '<=',
                'type' => 'NUMERIC',
            );
        }

        if (!empty($_GET['rep_city'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'rep_city',
                    'field' => 'term_id',
                    'terms' => absint($_GET['rep_city']),
                ),
            );
        }

        $query = new WP_Query($args);

        echo '<div class="rep-results">';

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                echo '<article class="rep-card">';

                if (has_post_thumbnail()) {
                    echo '<a href="' . esc_url(get_permalink()) . '">';
                    the_post_thumbnail('medium');
                    echo '</a>';
                }

                echo '<h3><a href="' . esc_url(get_permalink()) . '">';
                the_title();
                echo '</a></h3>';

                $price = get_post_meta(get_the_ID(), '_rep_price', true);
                $area = get_post_meta(get_the_ID(), '_rep_area', true);

                echo '<p>متراژ: ' . esc_html($area) . ' متر</p>';
                echo '<p>قیمت: ' . esc_html($price) . '</p>';
                echo '</article>';
            }

            wp_reset_postdata();
        } else {
            echo '<p>آگهی متناسبی پیدا نشد.</p>';
        }

        echo '</div>';
    }
}
