<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
$user_id   = $current_user->ID;
$user_name = $current_user->display_name;

wp_enqueue_script('chart');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'chart');
$number_days = '7';
$labels = array();
for ($i = $number_days; $i >= 0; $i--) {
    $date = strtotime(date("Y-m-d", strtotime("-" . $i . " day")));
    $labels[] = date('M j, Y', $date);
}

$args = array(
    'post_type' => 'service_order',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => 5,
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

$currency_sign_default = felan_get_option('currency_sign_default');
$withdraw_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
$withdraw_price = $currency_sign_default . felan_get_format_number($withdraw_price);
?>
<div class="felan-dashboard felan-dashboard-freelancer area-main-control">
    <div class="entry-my-page">
        <h2 class="entry-title"><?php echo sprintf(__('Welcome back! %s', 'felan-framework'), $user_name); ?></h2>
        <div class="total-action">
            <ul class="action-wrapper row">
                <li class="col-xl-3 col-sm-6">
                    <div class="icon-dashboard-03 felan-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('Posted Services', 'felan-framework'); ?></h3>
                            <span class="entry-number"><?php echo felan_total_service(); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-dashboard-04.svg'); ?>" alt="<?php esc_attr_e('interviews', 'felan-framework'); ?>">
                        </div>
                    </div>
                </li>
                <li class="col-xl-3 col-sm-6">
                    <div class="icon-dashboard-01 felan-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('Ordered Services', 'felan-framework'); ?></h3>
                            <span class="entry-number"><?php echo felan_total_my_service(); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-dashboard-01.svg'); ?>" alt="<?php esc_attr_e('jobs', 'felan-framework'); ?>">
                        </div>
                    </div>
                </li>
                <li class="col-xl-3 col-sm-6">
                    <div class="icon-dashboard-04 felan-boxdb">
                        <div class="entry-detail">
                            <h3 class="entry-title"><?php esc_html_e('Proposal Submitted', 'felan-framework'); ?></h3>
                            <span class="entry-number"><?php echo felan_total_projects_proposal(); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-dashboard-03.svg'); ?>" alt="<?php esc_attr_e('freelancers', 'felan-framework'); ?>">
                        </div>
                    </div>
                </li>
                <li class="col-xl-3 col-sm-6">
                    <div class="icon-dashboard-02 felan-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('Revenue Earned', 'felan-framework'); ?></h3>
                            <span class="entry-number"><?php echo $withdraw_price; ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-dashboard-02.svg'); ?>" alt="<?php esc_attr_e('applications', 'felan-framework'); ?>">
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="notification-dashboard">
            <div class="row">
                <div class="col-md-7">
                    <div class="felan-chart-warpper felan-chart-freelancer">
                        <div class="chart-header">
                            <h4 class="title-chart"><?php esc_html_e('Profile Views', 'felan-framework'); ?></h4>
                            <div class="form-chart">
                                <div class="select2-field">
                                    <select name="chart_freelancer" class="felan-select2">
                                        <option value="7"><?php esc_html_e('7 days', 'felan-framework'); ?></option>
                                        <option value="15"><?php esc_html_e('15 days', 'felan-framework'); ?></option>
                                        <option value="30"><?php esc_html_e('30 days', 'felan-framework'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <canvas id="felan-dashboard_freelancer" data-labels="<?php echo esc_attr(json_encode($labels)); ?>" data-values="<?php echo esc_attr(json_encode(felan_total_view_freelancer($number_days))); ?>" data-label="<?php esc_attr_e('Profile Views', 'felan-framework'); ?>">
                        </canvas>
                        <div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="jobs-dashboard-wrap">
                        <h4 class="title-jobs"><?php esc_html_e('Recent Service Orders', 'felan-framework'); ?></h4>
                        <div class="jobs-innner">
                            <?php if ($data->have_posts()) { ?>
                                <div class="jobs-content">
                                    <?php while ($data->have_posts()) : $data->the_post();
                                        global $current_user;
                                        wp_get_current_user();
                                        $user_id = $current_user->ID;
                                        $order_id = get_the_ID();
                                        $service_id = get_post_meta($order_id, FELAN_METABOX_PREFIX . 'service_order_item_id', true);
                                        $thumbnail = get_the_post_thumbnail_url($service_id, '70x70');
                                        $service_skills = get_the_terms($service_id, 'service-skills');
                                        $service_categories = get_the_terms($service_id, 'service-categories');
                                        $service_location = get_the_terms($service_id, 'felan_location');
                                        $author_id = get_post_field('post_author', $order_id);
                                        $author_name = get_the_author_meta('display_name', $author_id);
                                    ?>
                                        <div class="company-header">
                                            <div class="img-comnpany">
                                                <?php if (!empty($thumbnail)) : ?>
                                                    <img class="logo-company" src="<?php echo $thumbnail; ?>" alt="" />
                                                <?php else : ?>
                                                    <div class="logo-company"><i class="far fa-camera"></i></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="info-jobs">
                                                <h3 class="title-jobs-dashboard">
                                                    <a href="<?php echo get_permalink($service_id); ?>" target="_blank">
                                                        <?php echo get_the_title($service_id); ?>
                                                        <i class="far fa-external-link-alt"></i>
                                                    </a>
                                                </h3>
                                                <div>
                                                    <span><?php echo esc_html__('by', 'felan-framework') ?></span>
                                                    <span class="author" style="color: var(--felan-color-accent);"><?php echo esc_html($author_name); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php } else { ?>
                                <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
                            <?php } ?>
                        </div>
                        <a href="<?php echo esc_url(felan_get_permalink('freelancer_service')) ?>" class="felan-button button-block button-outline button-rounded"><?php esc_html_e('All Ordered', 'felan-framework'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>