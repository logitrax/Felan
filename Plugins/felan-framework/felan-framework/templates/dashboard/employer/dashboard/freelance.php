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

$show_employer_projects = felan_get_option('show_employer_projects', '1');
$currency_sign_default = felan_get_option('currency_sign_default');
$sending_price = felan_total_employer_sending($user_id);

$args_project = array(
    'post_type'           => 'project',
    'post_status'         => array('publish', 'expired', 'pending', 'pause'),
    'ignore_sticky_posts' => 1,
    'posts_per_page'      => -1,
    'author'              => $user_id,
);
$total_applications = 0;
$data_project = new WP_Query($args_project);
if ($data_project->have_posts()) {
    while ($data_project->have_posts()) : $data_project->the_post();
        $project_id = get_the_ID();
        $total_applications += felan_total_applications_project_id($project_id);
    endwhile;
}
?>
<div class="felan-dashboard felan-dashboard-employer area-main-control">
    <div class="entry-my-page">
        <h2 class="entry-title"><?php echo sprintf(__('Welcome back! %s', 'felan-framework'), $user_name); ?></h2>
        <div class="total-action">
            <ul class="action-wrapper row">
                <?php if ($show_employer_projects) : ?>
                    <li class="col-xl-3 col-sm-6">
                        <div class="icon-dashboard-01 felan-boxdb">
                            <div class="entry-detai ">
                                <h3 class="entry-title"><?php esc_html_e('Posted Projects', 'felan-framework'); ?></h3>
                                <span class="entry-number"><?php echo felan_total_actived_project($user_id); ?></span>
                            </div>
                            <div class="icon-total">
                                <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-dashboard-01.svg'); ?>" alt="<?php esc_attr_e('jobs', 'felan-framework'); ?>">
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
                <li class="col-xl-3 col-sm-6">
                    <div class="icon-dashboard-04 felan-boxdb">
                        <div class="entry-detail">
                            <h3 class="entry-title"><?php esc_html_e('Proposals Received', 'felan-framework'); ?></h3>
                            <span class="entry-number"><?php echo esc_html($total_applications); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-dashboard-04.svg'); ?>" alt="<?php esc_attr_e('freelancers', 'felan-framework'); ?>">
                        </div>
                    </div>
                </li>
                <li class="col-xl-3 col-sm-6">
                    <div class="icon-dashboard-03 felan-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('Bought Services', 'felan-framework'); ?></h3>
                            <span class="entry-number"><?php echo felan_total_employer_service_order(); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/icon-dashboard-03.svg'); ?>" alt="<?php esc_attr_e('jobs', 'felan-framework'); ?>">
                        </div>
                    </div>
                </li>
                <li class="col-xl-3 col-sm-6">
                    <div class="icon-dashboard-02 felan-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('Total Spending', 'felan-framework'); ?></h3>
                            <span class="entry-number"><?php echo $sending_price; ?></span>
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
                        <canvas id="felan-dashboard_employer" data-labels="<?php echo esc_attr(json_encode($labels)); ?>" data-values="<?php echo esc_attr(json_encode(felan_total_view_project($number_days))); ?>" data-label="<?php esc_attr_e('Page View', 'felan-framework'); ?>">
                        </canvas>
                        <div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="applicants-wrap">
                        <h4 class="title-applicants"><?php esc_html_e('Recent Project Proposals', 'felan-framework'); ?></h4>
                        <div class="applicants-innner">
                            <?php
                            global $current_user;
                            $user_id = $current_user->ID;
                            $args_project = apply_filters(
                                'felan/dashboard/employer/applicants/args_project',
                                array(
                                    'post_type' => 'project',
                                    'post_status' => 'publish',
                                    'ignore_sticky_posts' => 1,
                                    'posts_per_page' => -1,
                                    'author' => $user_id,
                                    'orderby' => 'date',
                                )
                            );

                            $data_project = new WP_Query($args_project);
                            $project_employer_id = array();
                            if ($data_project->have_posts()) {
                                while ($data_project->have_posts()) : $data_project->the_post();
                                    $project_employer_id[] = get_the_ID();
                                endwhile;
                            }

                            $args_proposals = array(
                                'post_type' => 'project-proposal',
                                'ignore_sticky_posts' => 1,
                                'posts_per_page' => 5,
                                'meta_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'key' => FELAN_METABOX_PREFIX . 'proposal_project_id',
                                        'value' => $project_employer_id,
                                        'compare' => 'IN'
                                    )
                                ),
                            );
                            $data_proposals = new WP_Query($args_proposals);

                            if ($data_proposals->have_posts() && !empty($project_employer_id)) {
                                while ($data_proposals->have_posts()) : $data_proposals->the_post();
                                    $proposals_id = get_the_ID();
                                    $project_id = get_post_meta($proposals_id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
                                    $author_id = get_post_field('post_author', $proposals_id);
                                    $freelancer_id = '';
                                    if (!empty($author_id)) {
                                        $args_freelancer = array(
                                            'post_type' => 'freelancer',
                                            'posts_per_page' => 1,
                                            'author' => $author_id,
                                        );
                                        $current_user_posts = get_posts($args_freelancer);
                                        $freelancer_id = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
                                        $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                                    }                            ?>
                                    <div class="applicants-content">
                                        <?php if (!empty($freelancer_avatar)) : ?>
                                            <div class="image-applicants"><img src="<?php echo esc_url($freelancer_avatar) ?>" alt="" /></div>
                                        <?php else : ?>
                                            <div class="image-applicants"><i class="far fa-camera"></i></div>
                                        <?php endif; ?>
                                        <?php if (!empty(get_the_title())) { ?>
                                            <div class="content">
                                                <?php if (!empty(get_the_author())) { ?>
                                                    <h6 style="margin-bottom: 2px">
                                                        <a href="<?php echo get_post_permalink($freelancer_id); ?>" target="_blank"><?php echo get_the_author(); ?></a>
                                                    </h6>
                                                <?php } else { ?>
                                                    <h6><?php esc_html_e('User not logged in', 'felan-framework'); ?></h6>
                                                <?php } ?>
                                                <p><?php esc_html_e('Applied:', 'felan-framework') ?>
                                                    <a href="<?php echo esc_url(get_permalink($project_id)); ?>" target="_blank" style="color: var(--felan-color-accent);">
                                                        <span> <?php esc_html_e(get_the_title()); ?></span>
                                                    </a>
                                                </p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php } else { ?>
                                <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
                            <?php } ?>
                        </div>
                        <a href="<?php echo esc_url(felan_get_permalink('projects')) ?>" class="felan-button button-outline button-rounded"><?php esc_html_e('All proposals', 'felan-framework'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>