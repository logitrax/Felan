<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer-service-order');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'freelancer-service-order',
    'felan_service_order_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_service' => esc_html__('No service found', 'felan-framework'),
    )
);

global $current_user;
$user_id = $current_user->ID;
$felan_freelancer = new Felan_freelancer_package();
$check_freelancer_package = $felan_freelancer->user_freelancer_package_available($user_id);
$posts_per_page = 10;
$args = array(
    'post_type' => 'service_order',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'post_status' => 'publish',
    'meta_query' => array(
        array(
            'key' => FELAN_METABOX_PREFIX . 'service_order_author_id',
            'value' => $user_id,
            'compare' => '==',
        )
    ),
);
$data = new WP_Query($args);

?>
<div class="felan-service-order entry-my-page">
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="select2-field">
                <select class="search-control felan-select2" name="service_status">
                    <option value=""><?php esc_html_e('All status', 'felan-framework') ?></option>
                    <option value="inprogress"><?php esc_html_e('In Process', 'felan-framework') ?></option>
                    <option value="canceled"><?php esc_html_e('Canceled', 'felan-framework') ?></option>
                    <option value="completed"><?php esc_html_e('Completed', 'felan-framework') ?></option>
                </select>
            </div>
            <div class="action-search">
                <input class="service-search-control" type="text" name="service_search"
                    placeholder="<?php esc_attr_e('Search service title', 'felan-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'felan-framework') ?></label>
            <div class="select2-field">
                <select class="search-control action-sorting felan-select2" name="service_sort_by">
                    <option value="newest"><?php esc_html_e('Newest', 'felan-framework') ?></option>
                    <option value="oldest"><?php esc_html_e('Oldest', 'felan-framework') ?></option>
                </select>
            </div>
        </div>
    </div>
    <?php if ($data->have_posts()) { ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard <?php if ($check_freelancer_package == -1 || $check_freelancer_package == 0) {
                                                echo 'expired';
                                            } ?>" id="freelancer-service-order">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Service', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Date', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Price', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Status', 'felan-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data->have_posts()) : $data->the_post(); ?>
                        <?php
                        global $current_user;
                        $user_id = $current_user->ID;
                        $order_id = get_the_ID();
                        $service_id = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                        $service_skills = get_the_terms($service_id, 'service-skills');
                        $service_categories = get_the_terms($service_id, 'service-categories');
                        $service_location = get_the_terms($service_id, 'felan_location');
                        $public_date = get_the_date(get_option('date_format'));
                        $thumbnail = get_the_post_thumbnail_url($service_id, '70x70');
                        $service_featured = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_featured', true);
                        $author_id = get_post_field('post_author', $order_id);
                        $author_name = get_the_author_meta('display_name', $author_id);
                        $status = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', true);
                        $service_refund_content = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_refund_content', true);

                        $currency_sign_default = felan_get_option('currency_sign_default');
                        $currency_position = felan_get_option('currency_position');
                        $price_order = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_price', true);
                        $price_order_number = str_replace($currency_sign_default, '', $price_order);
                        $enable_freelancer_service_fee = felan_get_option('enable_freelancer_service_fee');
                        $freelancer_number_service_fee = felan_get_option('freelancer_number_service_fee');
                        $price_fee = round(intval($price_order_number) * intval($freelancer_number_service_fee) / 100);

                        $active_date = strtotime(get_the_date('Y-m-d H:i:s'));
                        $current_time = strtotime(current_datetime()->format('Y-m-d H:i:s'));
                        $service_time_type = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_time_type', true);
                        $number_delivery_time = intval(get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_number_time', true));
                        switch ($service_time_type) {
                            case 'hr':
                                $seconds = 60 * 60;
                                break;
                            case 'day':
                                $seconds = 60 * 60 * 24;
                                break;
                            case 'week':
                                $seconds = 60 * 60 * 24 * 7;
                                break;
                            case 'month':
                                $seconds = 60 * 60 * 24 * 30;
                                break;
                        }
                        if (is_numeric($active_date) && is_numeric($seconds) && is_numeric($number_delivery_time)) {
                            $expired_time = $active_date + ($seconds * $number_delivery_time);
                        } else {
                            $expired_time = 0;
                        }

                        if ($current_time < $expired_time) {
                            $seconds = $expired_time - $current_time;
                            $dtF = new \DateTime('@0');
                            $dtT = new \DateTime("@$seconds");
                            $expired_days = $dtF->diff($dtT)->format('%a');
                            $expired_hours = $dtF->diff($dtT)->format('%h');
                            $expired_minutes = $dtF->diff($dtT)->format('%i');
                            if ($expired_days > 0) {
                                if ($expired_days === '1') {
                                    $expired_date = sprintf(esc_html__('%1s day %2s hours', 'felan-framework'), $expired_days, $expired_hours);
                                } else {
                                    $expired_date = sprintf(esc_html__('%1s days %2s hours', 'felan-framework'), $expired_days, $expired_hours);
                                }
                            } else {
                                if ($expired_hours === '1') {
                                    $expired_date = sprintf(esc_html__('%1s hour %2s minutes', 'felan-framework'), $expired_hours, $expired_minutes);
                                } else {
                                    $expired_date = sprintf(esc_html__('%1s hours %2s minutes', 'felan-framework'), $expired_hours, $expired_minutes);
                                }
                            }
                        } else {
                            $expired_date = esc_html__('Expired', 'felan-framework');
                        }
                        $status = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_payment_status', true);
                        ?>
                        <tr>
                            <td>
                                <div class="service-header">
                                    <?php if (!empty($thumbnail)) : ?>
                                        <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                                    <?php endif; ?>
                                    <div class="content">
                                        <h3 class="title-my-service">
                                            <a href="<?php echo get_the_permalink($service_id) ?>">
                                                <?php echo get_the_title($service_id); ?>
                                                <?php if ($service_featured === '1') : ?>
                                                    <span class="tooltip featured"
                                                        data-title="<?php esc_attr_e('Featured', 'felan-framework') ?>">
                                                        <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>"
                                                            alt="<?php echo esc_attr('featured', 'felan-framework') ?>">
                                                    </span>
                                                <?php endif; ?>
                                            </a>
                                        </h3>
                                        <div class="info-service-inner">
                                            <?php echo felan_get_total_rating('service', $service_id, false); ?>
                                            <div class="count-sales">
                                                <i class="fal fa-shopping-basket"></i>
                                                <?php echo felan_service_count_sale($user_id, $service_id); ?>
                                            </div>
                                            <?php felan_total_view_service_details($service_id); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="start-time">
                                <span>
                                    <?php echo esc_html__('Order date: ', 'felan-framework') ?>
                                    <span class="time"><?php echo $public_date; ?></span>
                                </span>
                                <span>
                                    <?php echo esc_html__('Deadline: ', 'felan-framework') ?>
                                    <span class="time"><?php echo $expired_date; ?></span>
                                </span>
                            </td>
                            <td class="price">
                                <?php echo $price_order; ?>
                            </td>
                            <td class="status">
                                <?php felan_service_order_status($status); ?>
                            </td>
                            <td>
                                <a href="<?php echo esc_url(felan_get_permalink('freelancer_service')); ?>?order_id=<?php echo esc_attr($order_id); ?>"
                                    class="service-detail felan-button"><?php echo esc_html__('Detail', 'felan-framework') ?></a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>
        </div>
    <?php } else { ?>
        <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
    <?php } ?>
    <?php $total_post = $data->found_posts;
    if ($total_post > $posts_per_page) { ?>
        <div class="pagination-dashboard pagination-wishlist">
            <?php $max_num_pages = $data->max_num_pages;
            felan_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
            wp_reset_postdata(); ?>
        </div>
    <?php } ?>
</div>