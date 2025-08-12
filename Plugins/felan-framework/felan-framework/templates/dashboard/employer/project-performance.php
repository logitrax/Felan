<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$project_id = isset($_GET['project_id']) ? felan_clean(wp_unslash($_GET['project_id'])) : '';
$action = 'submit-meetings';

wp_enqueue_script('chart');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'chart');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'meetings');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'meetings',
    'felan_meetings_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_applicants' => esc_html__('No meetings found', 'felan-framework'),
    )
);
$number_days = '7';
$labels = array();
for ($i = $number_days; $i >= 0; $i--) {
    $date = strtotime(date("Y-m-d", strtotime("-" . $i . " day")));
    $labels[] = date('M j, Y', $date);
}

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'project-applicants');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'project-applicants',
    'felan_project_applicants_vars',
    array(
        'ajax_url'       => FELAN_AJAX_URL,
        'not_applicants' => esc_html__('No applicants found', 'felan-framework'),
    )
);

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'applicants-dashboard');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'applicants-dashboard',
    'felan_applicants_dashboard_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_applicants' => esc_html__('No applicants found', 'felan-framework'),
    )
);

$id = get_the_ID();
$posts_per_page = 10;
global $current_user;
$user_id = $current_user->ID;
$tab_active = isset($_GET['tab']) ? felan_clean(wp_unslash($_GET['tab'])) : '';

$args_applicants = array(
    'post_type'           => 'project-proposal',
    'ignore_sticky_posts' => 1,
    'posts_per_page'      => $posts_per_page,
    'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'meta_query'          => array(
        'relation' => 'AND',
        array(
            'key'     => FELAN_METABOX_PREFIX . 'proposal_project_id',
            'value'   => $project_id,
            'compare' => '='
        )
    ),
);
$data_applicants = new WP_Query($args_applicants);
?>

<div class="entry-my-page project-performance-dashboard mettings-action-dashboard">
    <div class="tab-dashboard-active">
        <ul class="tab-list-active">
            <li class="tab-item <?php if ($tab_active == 'statics') { ?>active<?php } ?>"><a href="#tab-statics"><?php esc_html_e('Statics', 'felan-framework'); ?></a></li>
            <li class="tab-item <?php if ($tab_active == 'applicants') { ?>active<?php } ?>"><a href="#tab-applicants"><?php esc_html_e('Applicants', 'felan-framework'); ?>
                    (<?php esc_html_e($data_applicants->found_posts) ?>)</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-info-active <?php if ($tab_active == 'statics') { ?>active<?php } ?>" id="tab-statics">
                <div class="felan-chart-project-warpper">
                    <div class="chart-header">
                        <h4 class="title-chart"><?php esc_html_e('Project views', 'felan-framework'); ?></h4>
                        <div class="form-chart">
                            <div class="select2-field">
                                <select name="project-chart-date" class="felan-select2">
                                    <option value="7"><?php esc_html_e('7 days', 'felan-framework'); ?></option>
                                    <option value="15"><?php esc_html_e('15 days', 'felan-framework'); ?></option>
                                    <option value="30"><?php esc_html_e('30 days', 'felan-framework'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <canvas id="felan-dashboard_project_chart" data-labels="<?php echo esc_attr(json_encode($labels)); ?>" data-values_view="<?php echo esc_attr(json_encode(felan_view_project_date($project_id, $number_days))); ?>" data-label_view="<?php esc_attr_e('Page View', 'felan-framework'); ?>" data-values_apply="<?php echo esc_attr(json_encode(felan_total_project_apply($project_id, $number_days))); ?>" data-label_apply="<?php esc_attr_e('Apply Click', 'felan-framework'); ?>" data-project-id="<?php echo $project_id ?>">
                    </canvas>
                    <div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>
                </div>
            </div>
            <div class="tab-info-active applicants-dashboard project_details <?php if ($tab_active == 'applicants') { ?>active<?php } ?>" id="tab-applicants">
                <div class="search-dashboard-warpper">
                    <div class="search-left">
                        <div class="action-search">
                            <input class="search-control" type="text" name="applicants_search" placeholder="<?php esc_attr_e('Find by name', 'felan-framework') ?>">
                            <button class="btn-search">
                                <i class="far fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="search-right">
                        <label class="text-sorting"><?php esc_html_e('Sort by', 'felan-framework') ?></label>
                        <div class="select2-field">
                            <select class="search-control action-sorting felan-select2" name="applicants_sort_by">
                                <option value="newest"><?php esc_html_e('Newest', 'felan-framework') ?></option>
                                <option value="oldest"><?php esc_html_e('Oldest', 'felan-framework') ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php if ($data_applicants->have_posts() && !empty($project_id)) { ?>
                    <div class="table-dashboard-wapper">
                        <table class="table-dashboard" id="my-applicants">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Name', 'felan-framework') ?></th>
                                    <th><?php esc_html_e('Status', 'felan-framework') ?></th>
                                    <th><?php esc_html_e('Information', 'felan-framework') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($data_applicants->have_posts()) : $data_applicants->the_post(); ?>
                                    <?php
                                    $id = get_the_ID();
                                    global $current_user;
                                    wp_get_current_user();
                                    $user_id            = $current_user->ID;
                                    $public_date        = get_the_date(get_option('date_format'));
                                    $project_id            = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
                                    $proposal_price   = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_price', true);
                                    $proposal_time   = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_time', true);
                                    $proposal_time_type   = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_time_type', true);
                                    $proposal_message = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_message', true);
                                    $proposal_status  = get_post_meta($id, FELAN_METABOX_PREFIX . 'applicants_status', true);
                                    $author_id          = get_post_field('post_author', $id);

                                    $freelancer_id       = '';
                                    if (!empty($author_id)) {
                                        $args_freelancer     = array(
                                            'post_type'      => 'freelancer',
                                            'posts_per_page' => 1,
                                            'author'         => $author_id,
                                        );
                                        $current_user_posts = get_posts($args_freelancer);
                                        $freelancer_id       = !empty($current_user_posts) ? $current_user_posts[0]->ID : '';
                                        $freelancer_avatar   = get_the_author_meta('author_avatar_image_url', $author_id);
                                    }
                                    $read_mess  = get_post_meta($id, FELAN_METABOX_PREFIX . 'read_mess', true);
                                    $reply_mess = get_post_meta($id, FELAN_METABOX_PREFIX . 'reply_mess', true);
                                    $project_currency_type = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_currency_type', true);
                                    $currency_position = felan_get_option('currency_position');
                                    $currency_leff = $currency_right = '';
                                    if ($currency_position == 'before') {
                                        $currency_leff = $project_currency_type;
                                    } else {
                                        $currency_right = $project_currency_type;
                                    }
                                    ?>
                                    <tr>
                                        <td class="info-user">
                                            <?php if (!empty($freelancer_avatar)) : ?>
                                                <div class="image-applicants"><img class="image-freelancers" src="<?php echo esc_url($freelancer_avatar) ?>" alt="" /></div>
                                            <?php else : ?>
                                                <div class="image-applicants"><i class="far fa-camera"></i></div>
                                            <?php endif; ?>
                                            <div class="info-details">
                                                <?php if (!empty(get_the_author())) { ?>
                                                    <h3>
                                                        <a href="<?php echo get_post_permalink($freelancer_id); ?>"><?php echo get_the_author(); ?></a>
                                                    </h3>
                                                <?php } else { ?>
                                                    <h3><?php esc_html_e('User not logged in', 'felan-framework'); ?></h3>
                                                <?php } ?>
                                                <?php if (!empty(get_the_title())) { ?>
                                                    <div class="applied"><?php esc_html_e('Applied:', 'felan-framework') ?>
                                                        <a href="<?php echo esc_url(get_permalink($project_id)); ?>" target="_blank">
                                                            <span> <?php esc_html_e(get_the_title()); ?></span>
                                                            <i class="far fa-external-link-alt"></i>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td class="status">
                                            <div class="approved">
                                                <?php echo felan_applicants_status($id); ?>
                                                <span class="applied-time"><?php esc_html_e('Applied:', 'felan-framework') ?><?php esc_html_e($public_date) ?></span>
                                            </div>
                                        </td>
                                        <td class="info">
                                            <span><?php echo sprintf(esc_html__('%1s%2s%3s/%4s%5s', 'felan-framework'), $currency_leff, $proposal_price, $currency_right, $proposal_time, $proposal_time_type) ?></span>
                                        </td>
                                        <td class="applicants-control action-setting">
                                            <div class="list-action">
                                                <?php if (!empty(get_the_author())) { ?>
                                                    <a href="#" class="action icon-video tooltip btn-reschedule-meetings" data-id="<?php echo esc_attr($id); ?>" data-title="<?php esc_attr_e('Meetings', 'felan-framework') ?>"><i class="far fa-video-plus"></i></a>
                                                    <?php if ($reply_mess !== 'yes') : ?>
                                                        <a href="#" class="action icon-messages tooltip" id="btn-mees-applicants" data-apply="<?php esc_html_e(get_the_title()); ?>" data-id="<?php echo esc_attr($id); ?>" data-mess="<?php echo $proposal_message; ?>" data-project-id="<?php echo $project_id; ?>" data-title="<?php esc_attr_e('Messages', 'felan-framework') ?>">
                                                            <i class="far fa-comment-dots <?php if ($read_mess === 'yes') {
                                                                                                    echo 'active';
                                                                                                } ?>"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php } ?>
                                                <div class="action">
                                                    <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                                                    <ul class="action-dropdown">
                                                        <?php if (empty($proposal_status)) { ?>
                                                            <li><a class="btn-approved" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Approved', 'felan-framework') ?></a></li>
                                                            <li><a class="btn-rejected" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Rejected', 'felan-framework') ?></a></li>
                                                            <?php } else {
                                                            if ($proposal_status == 'approved') { ?>
                                                                <li><a class="btn-rejected" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Rejected', 'felan-framework') ?></a>
                                                                </li>
                                                            <?php } else { ?>
                                                                <li><a class="btn-approved" applicants-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Approved', 'felan-framework') ?></a>
                                                                </li>
                                                        <?php }
                                                        } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <input type="hidden" name="link_mess" value="<?php echo felan_get_permalink('messages'); ?>">
                        </table>
                        <div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>
                        <input name="proposal_project_id" type="hidden" value="<?php echo esc_attr($project_id); ?>" />
                    </div>
                <?php } else { ?>
                    <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
                <?php } ?>
                <?php $total_post = $data_applicants->found_posts;
                if ($total_post > $posts_per_page && !empty($project_id)) { ?>
                    <div class="pagination-dashboard">
                        <?php $max_num_pages = $data_applicants->max_num_pages;
                        felan_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
                        wp_reset_postdata(); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <input type="hidden" name="mettings_action" value="<?php echo esc_attr($action) ?>" />
</div>