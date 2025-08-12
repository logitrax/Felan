<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$service_id = isset($_GET['service_id']) ? felan_clean(wp_unslash($_GET['service_id'])) : '';
$felan_freelancer_package = new Felan_freelancer_package();
$check_freelancer_package = $felan_freelancer_package->user_freelancer_package_available($user_id);

$order_id = isset($_GET['order_id']) ? felan_clean(wp_unslash($_GET['order_id'])) : '';

$args = array(
    'post_type' => 'service',
    'post_status' => array('publish', 'expired'),
    'posts_per_page' => -1,
    'author' => $user_id,
);
$data = new WP_Query($args);
$posts = $data->posts;
foreach ($posts as $post) {
    $id_ex = $post->ID;
    if ($check_freelancer_package == -1 || $check_freelancer_package == 0) {
        update_post_meta($id_ex, FELAN_METABOX_PREFIX . 'enable_service_package_expires', 1);
    } else {
        update_post_meta($id_ex, FELAN_METABOX_PREFIX . 'enable_service_package_expires', 0);
    }
}

if (!empty($order_id)) { ?>
    <div class="felan-service-dashboard entry-my-page">
        <div class="entry-title">
            <h4><?php esc_html_e('Service History', 'felan-framework'); ?></h4>
        </div>
        <?php felan_get_template('dashboard/freelancer/service/order-detail.php'); ?>
    </div>
<?php } else {
    if (!empty($service_id)) {
        felan_get_template('dashboard/freelancer/service/my-service.php');
    } else { ?>
        <?php if ($check_freelancer_package == -1 || $check_freelancer_package == 0) : ?>
            <p class="notice"><i class="far fa-exclamation-circle"></i>
                <?php echo esc_html__('Package expired. Please select a ', 'felan-framework') .
                    '<a href="' . esc_url(felan_get_permalink('freelancer_package')) . '">' . esc_html__('new one', 'felan-framework') . '</a>.';
                ?>
            </p>
        <?php endif; ?>
        <div class="felan-service-dashboard entry-my-page">
            <?php felan_withdraw_noti_print('featured_service','You will be charged %s for a featured service.'); ?>
            <div class="entry-title">
                <h4><?php esc_html_e('My Services', 'felan-framework'); ?></h4>
                <div class="button-warpper">
                    <?php if ($check_freelancer_package == -1 || $check_freelancer_package == 0) : ?>
                        <a href="<?php echo get_permalink(felan_get_option('felan_freelancer_package_page_id')); ?>"
                           class="felan-button">
                            <i class="far fa-plus"></i><?php esc_html_e('Create new service', 'felan-framework') ?>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo get_permalink(felan_get_option('felan_submit_service_page_id')); ?>"
                           class="felan-button">
                            <i class="far fa-plus"></i><?php esc_html_e('Create new service', 'felan-framework') ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tab-dashboard">
                <ul class="tab-list">
                    <li class="tab-item tab-service-item"><a
                                href="#tab-service"><?php esc_html_e('My Services', 'felan-framework'); ?></a></li>
                    <li class="tab-item tab-orders-item"><a
                                href="#tab-orders"><?php esc_html_e('Orders', 'felan-framework'); ?></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-info" id="tab-service">
                        <?php felan_get_template('dashboard/freelancer/service/my-service.php'); ?>
                    </div>
                    <div class="tab-info" id="tab-orders">
                        <?php felan_get_template('dashboard/freelancer/service/my-orders.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php }
}