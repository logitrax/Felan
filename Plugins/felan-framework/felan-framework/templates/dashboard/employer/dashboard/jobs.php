<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!is_user_logged_in()) {
    felan_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}
global $current_user;
$user_id = $current_user->ID;
$user_name = $current_user->display_name;

wp_enqueue_script('chart');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'chart');
$number_days = '7';
$labels = array();
for ($i = $number_days; $i >= 0; $i--) {
    $date = strtotime(date("Y-m-d", strtotime("-" . $i . " day")));
    $labels[] = date('M j, Y', $date);
}

$args_jobs = array(
    'post_type' => 'jobs',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => -1,
    'author' => $user_id,
    'orderby' => 'date',
);
$data_jobs = new WP_Query($args_jobs);
$jobs_employer_id = array();
if ($data_jobs->have_posts()) {
    while ($data_jobs->have_posts()) : $data_jobs->the_post();
        $jobs_employer_id[] = get_the_ID();
    endwhile;
}

$show_jobs_dashboard = felan_get_option( 'show_employer_jobs_dashboard', '1' );
$show_applicants = felan_get_option( 'show_employer_applicants', '1' );
$show_meetings = felan_get_option( 'show_employer_meetings', '1' );
$show_freelancers = felan_get_option( 'show_employer_freelancers', '1' )
?>
<div class="felan-dashboard felan-dashboard-employer area-main-control">
    <div class="entry-my-page">
        <h2 class="entry-title"><?php echo sprintf(__('Welcome back! %s', 'felan-framework'), $user_name); ?></h2>
        <div class="total-action">
            <ul class="action-wrapper row">
                <?php if($show_jobs_dashboard) : ?>
                    <li class="col-xl-3 col-sm-6">
                        <div class="icon-dashboard-01 felan-boxdb">
                            <div class="entry-detai ">
                                <h3 class="entry-title"><?php esc_html_e('Posted Jobs', 'felan-framework'); ?></h3>
                                <span class="entry-number"><?php echo felan_total_actived_jobs(); ?></span>
                            </div>
                            <div class="icon-total">
                                <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-dashboard-01.svg'); ?>" alt="<?php esc_attr_e('jobs', 'felan-framework'); ?>">
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if($show_applicants) : ?>
                    <li class="col-xl-3 col-sm-6">
                        <div class="icon-dashboard-04 felan-boxdb">
                            <div class="entry-detai ">
                                <h3 class="entry-title"><?php esc_html_e('Applicants', 'felan-framework'); ?></h3>
                                <span class="entry-number"><?php echo felan_total_applications_jobs(); ?></span>
                            </div>
                            <div class="icon-total">
                                <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-dashboard-04.svg'); ?>"
                                     alt="<?php esc_attr_e('applications', 'felan-framework'); ?>">
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if($show_meetings) : ?>
                    <li class="col-xl-3 col-sm-6">
                        <div class="icon-dashboard-02 felan-boxdb">
                            <div class="entry-detai ">
                                <h3 class="entry-title"><?php esc_html_e('MEETINGS', 'felan-framework'); ?></h3>
                                <span class="entry-number"><?php echo felan_total_meeting('employer'); ?></span>
                            </div>
                            <div class="icon-total">
                                <i class="far fa-video" style="color: #fff"></i>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if($show_freelancers) : ?>
                    <li class="col-xl-3 col-sm-6">
                        <div class="icon-dashboard-03 felan-boxdb">
                            <div class="entry-detail">
                                <h3 class="entry-title"><?php esc_html_e('My Follow', 'felan-framework'); ?></h3>
                                <span class="entry-number"><?php echo felan_total_post('freelancer','follow_freelancer'); ?></span>
                            </div>
                            <div class="icon-total">
                                <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-dashboard-03.svg'); ?>"
                                     alt="<?php esc_attr_e('freelancers', 'felan-framework'); ?>">
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="notification-dashboard">
            <div class="row">
                <div class="col-md-7">
                    <div class="felan-chart-warpper felan-chart-employer">
                        <div class="chart-header">
                            <h4 class="title-chart"><?php esc_html_e('Page views', 'felan-framework'); ?></h4>
                            <div class="form-chart">
                                <div class="select2-field">
                                    <select name="chart_employer" class="felan-select2">
                                        <option value="7"><?php esc_html_e('7 days', 'felan-framework'); ?></option>
                                        <option value="15"><?php esc_html_e('15 days', 'felan-framework'); ?></option>
                                        <option value="30"><?php esc_html_e('30 days', 'felan-framework'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <canvas id="felan-dashboard_employer" data-labels="<?php echo esc_attr(json_encode($labels)); ?>"
                                data-values="<?php echo esc_attr(json_encode(felan_total_view_jobs($number_days))); ?>"
                                data-label="<?php esc_attr_e('Page View', 'felan-framework'); ?>">
                        </canvas>
                        <div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="applicants-wrap">
                        <h4 class="title-applicants"><?php esc_html_e('New applicants', 'felan-framework'); ?></h4>
                        <div class="applicants-innner">
                            <div class="applicants-heading">
                                <?php
                                $args_title_one = array(
                                    'post_type' => 'applicants',
                                    'ignore_sticky_posts' => 1,
                                    'posts_per_page' => 1,
                                    'orderby' => 'date',
                                    'meta_query' => array(
                                        'relation' => 'AND',
                                        array(
                                            'key' => FELAN_METABOX_PREFIX . 'applicants_jobs_id',
                                            'value' => $jobs_employer_id,
                                            'compare' => 'IN'
                                        )
                                    ),
                                );
                                $data_title_one = new WP_Query($args_title_one);
                                $id_jobs_one = array();
                                if ($data_title_one->have_posts()) {
                                    while ($data_title_one->have_posts()) : $data_title_one->the_post();
                                        $id = get_the_ID();
                                        $id_jobs_one = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_jobs_id');
                                        if (!empty($id_jobs_one)) {
                                            $id_jobs_one = $id_jobs_one[0];
                                        }
                                    endwhile;
                                } ?>
                                <?php if (!empty($id_jobs_one)) {
                                    $id_jobs_one = intval($id_jobs_one);
                                } ?>
                                <?php if (!empty(($id_jobs_one) && !empty($jobs_employer_id))) : ?>
                                    <h3><?php esc_html_e(get_the_title($id_jobs_one)); ?></h3>
                                    <span><?php echo felan_total_applications_jobs_id($id_jobs_one) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php $args_applicants_one = array(
                                'post_type' => 'applicants',
                                'ignore_sticky_posts' => 1,
                                'posts_per_page' => 2,
                                'orderby' => 'date',
                                'meta_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'key' => FELAN_METABOX_PREFIX . 'applicants_jobs_id',
                                        'value' => $id_jobs_one,
                                        'compare' => '='
                                    )
                                ),
                            );
                            $data_applicants_one = new WP_Query($args_applicants_one);
                            if ($data_applicants_one->have_posts() && !empty($jobs_employer_id) && !empty($id_jobs_one)) {
                                while ($data_applicants_one->have_posts()) : $data_applicants_one->the_post();
                                    $id = get_the_ID();
                                    $jobs_id = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_jobs_id');
                                    $public_date = get_the_date(get_option('date_format'));
                                    $author_id = get_post_field( 'post_author', $id );
                                    $freelancer_avatar = get_the_author_meta( 'author_avatar_image_url', $author_id );
                                    ?>
                                    <div class="applicants-content">
                                        <?php if (!empty($freelancer_avatar)) : ?>
                                            <div class="image-applicants"><img src="<?php echo esc_url($freelancer_avatar) ?>" alt="" /></div>
                                        <?php else : ?>
                                            <div class="image-applicants"><i class="far fa-camera"></i></div>
                                        <?php endif; ?>
                                        <?php if (!empty(get_the_title())) { ?>
                                            <div class="content">
                                                <?php if (!empty(get_the_author())) : ?>
                                                    <h6><?php esc_html_e(get_the_author()); ?></h6>
                                                <?php endif; ?>
                                                <p><?php esc_html_e('Applied date: ', 'felan-framework') ?><?php echo $public_date ?></p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php } else { ?>
                                <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
                            <?php } ?>
                        </div>
                        <div class="applicants-innner">
                            <div class="applicants-heading">
                                <?php
                                $args_title_two = array(
                                    'post_type' => 'applicants',
                                    'ignore_sticky_posts' => 1,
                                    'posts_per_page' => 1,
                                    'orderby' => 'date',
                                    'meta_query' => array(
                                        'relation' => 'AND',
                                        array(
                                            'key' => FELAN_METABOX_PREFIX . 'applicants_jobs_id',
                                            'value' => $jobs_employer_id,
                                            'compare' => 'IN'
                                        ),
                                        array(
                                            'key' => FELAN_METABOX_PREFIX . 'applicants_jobs_id',
                                            'value' => $id_jobs_one,
                                            'compare' => '!='
                                        )
                                    ),
                                );
                                $data_title_two = new WP_Query($args_title_two);
                                $id_jobs_two = array();
                                if ($data_title_two->have_posts()) {
                                    while ($data_title_two->have_posts()) : $data_title_two->the_post();
                                        $id = get_the_ID();
                                        $id_jobs_two = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_jobs_id');
                                        if (!empty($id_jobs_two)) {
                                            $id_jobs_two = $id_jobs_two[0];
                                        }
                                    endwhile;
                                } ?>

                                <?php if (!empty($id_jobs_two)) {
                                    $id_jobs_two = intval($id_jobs_two);
                                } ?>
                                <?php if (!empty(($id_jobs_two) && !empty($jobs_employer_id))) : ?>
                                    <h3><?php esc_html_e(get_the_title($id_jobs_two)); ?></h3>
                                    <span><?php echo felan_total_applications_jobs_id($id_jobs_two) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php $args_applicants_two = array(
                                'post_type' => 'applicants',
                                'ignore_sticky_posts' => 1,
                                'posts_per_page' => 2,
                                'orderby' => 'date',
                                'meta_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'key' => FELAN_METABOX_PREFIX . 'applicants_jobs_id',
                                        'value' => $id_jobs_two,
                                        'compare' => '='
                                    ),
                                    array(
                                        'key' => FELAN_METABOX_PREFIX . 'applicants_jobs_id',
                                        'value' => $id_jobs_one,
                                        'compare' => '!='
                                    )
                                ),
                            );
                            $data_applicants_two = new WP_Query($args_applicants_two);
                            if ($data_applicants_two->have_posts() && !empty($jobs_employer_id) && !empty($id_jobs_two)) {
                                while ($data_applicants_two->have_posts()) : $data_applicants_two->the_post();
                                    $id = get_the_ID();
                                    $jobs_id = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_jobs_id');
                                    $public_date = get_the_date(get_option('date_format'));
                                    $author_id = get_post_field( 'post_author', $id );
                                    $freelancer_avatar = get_the_author_meta( 'author_avatar_image_url', $author_id );
                                    ?>
                                    <div class="applicants-content">
                                        <?php if (!empty($freelancer_avatar)) : ?>
                                            <div class="image-applicants"><img src="<?php echo esc_url($freelancer_avatar) ?>" alt="" /></div>
                                        <?php else : ?>
                                            <div class="image-applicants"><i class="far fa-camera"></i></div>
                                        <?php endif; ?>
                                        <?php if (!empty(get_the_title())) { ?>
                                            <div class="content">
                                                <?php if (!empty(get_the_author())) : ?>
                                                    <h6><?php esc_html_e(get_the_author()); ?></h6>
                                                <?php endif; ?>
                                                <p><?php esc_html_e('Applied date :', 'felan-framework') ?><?php echo $public_date ?></p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php } ?>
                        </div>
                        <a href="<?php echo esc_url(felan_get_permalink('jobs_dashboard')) ?>"
                           class="felan-button button-outline button-rounded"><?php esc_html_e('All applicants', 'felan-framework'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
