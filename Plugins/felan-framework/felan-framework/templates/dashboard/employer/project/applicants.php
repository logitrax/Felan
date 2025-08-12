<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
    felan_get_template('global/access-denied.php', array('type' => 'not_login'));

    return;
}

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'employer-review-freelancer');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'employer-review-freelancer',
    'felan_freelancer_review_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
    )
);

$id = get_the_ID();
$posts_per_page = 10;
$action = 'submit-meetings';
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'meetings');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'meetings',
    'felan_meetings_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_applicants' => esc_html__('No meetings found', 'felan-framework'),
    )
);
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'project-applicants');
$payment_url = felan_get_permalink('payment_project');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'project-applicants',
    'felan_project_applicants_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_applicants' => esc_html__('No applicants found', 'felan-framework'),
        'payment_url' => $payment_url,
    )
);

$payment_url = felan_get_permalink('payment_project');

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

$args_applicants = array(
    'post_type' => 'project-proposal',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => FELAN_METABOX_PREFIX . 'proposal_project_id',
            'value' => $project_employer_id,
            'compare' => 'IN'
        )
    ),
);
$data_applicants = new WP_Query($args_applicants);
?>

<div class="entry-my-page applicants-dashboard mettings-action-dashboard">
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="action-search">
                <input class="search-control" type="text" name="applicants_search" placeholder="<?php esc_attr_e('Find by project', 'felan-framework') ?>">
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
    <?php if ($data_applicants->have_posts() && !empty($project_employer_id)) {
    ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard" id="my-applicants">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Name', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Status', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Price', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Time', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Applied Date', 'felan-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data_applicants->have_posts()) : $data_applicants->the_post(); ?>
                        <?php
                        $id = get_the_ID();
                        global $current_user;
                        wp_get_current_user();
                        $user_id = $current_user->ID;
                        $public_date = get_the_date(get_option('date_format'));
                        $project_id = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
                        $proposal_price = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_price', true);
                        $proposal_time = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_time', true);
                        $proposal_time_type = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_time_type', true);
                        $proposal_message = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_message', true);
                        $proposal_status = get_post_meta($id, FELAN_METABOX_PREFIX . 'proposal_status', true);
                        $project_refund_content = get_post_meta($id, FELAN_METABOX_PREFIX . 'project_refund_content', true);
                        $author_id = get_post_field('post_author', $id);

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
                        }
                        $read_mess = get_post_meta($id, FELAN_METABOX_PREFIX . 'read_mess', true);
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
                                            <a href="<?php echo get_post_permalink($freelancer_id); ?>" target="_blank"><?php echo get_the_author(); ?></a>
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
                                <?php felan_project_package_status($proposal_status); ?>
                            </td>
                            <td>
                                <span><?php echo $currency_leff . $proposal_price . $currency_right; ?></span>
                            </td>
                            <td>
                                <span><?php echo sprintf(esc_html__('%1s %2s', 'felan-framework'), $proposal_time, $proposal_time_type) ?></span>
                            </td>
                            <td class="start-time">
                                <?php echo $public_date; ?>
                            </td>
                            <td class="applicants-control action-setting">
                                <div class="list-action">
                                    <?php if (!empty(get_the_author())) { ?>
                                        <a href="#" class="action icon-video tooltip btn-reschedule-meetings" data-id="<?php echo esc_attr($id); ?>" data-title="<?php esc_attr_e('Create a Meeting', 'felan-framework') ?>"><i class="far fa-video-plus"></i></a>
                                        <?php if ($reply_mess !== 'yes') : ?>
                                            <a href="#" class="action icon-messages tooltip" id="btn-mees-applicants" data-apply="<?php esc_html_e(get_the_title()); ?>" data-id="<?php echo esc_attr($id); ?>" data-mess="<?php echo $proposal_message; ?>" data-project-id="<?php echo $project_id; ?>" data-title="<?php esc_attr_e('Message Applicants', 'felan-framework') ?>">
                                                <i class="far fa-comment-dots <?php if ($read_mess === 'yes') {
                                                                                        echo 'active';
                                                                                    } ?>"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php } ?>
                                    <div class="action">
                                        <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                                        <ul class="action-dropdown">
                                            <?php switch ($proposal_status) {
                                                case 'completed': ?>
                                                    <li><a class="btn-action-review" freelancer-id="<?php echo $freelancer_id; ?>" href="#"><?php esc_html_e('Leave a review ', 'felan-framework') ?></a>
                                                    </li>
                                                <?php break;
                                                case 'transferring': ?>
                                                    <li><a class="btn-completed" order-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Complete', 'felan-framework') ?></a>
                                                    </li>
                                                    <li><a class="btn-order-refund" order-id="<?php echo esc_attr($id); ?>" href="#form-project-order-refund"><?php esc_html_e('Refund', 'felan-framework') ?></a>
                                                    </li>
                                                <?php break;
                                                case 'confirming': ?>
                                                    <li><a class="btn-order-refund" order-id="<?php echo esc_attr($id); ?>" href="#form-project-order-refund"><?php esc_html_e('Refund', 'felan-framework') ?></a>
                                                    </li>
                                                <?php break;
                                                case 'inprogress': ?>
                                                    <li><a class="btn-order-refund" order-id="<?php echo esc_attr($id); ?>" href="#form-project-order-refund"><?php esc_html_e('Refund', 'felan-framework') ?></a>
                                                    </li>
                                                <?php break;
                                                case 'pending': ?>
                                                    <li><a class="btn-accept-pay" data-id="<?php echo esc_attr($id); ?>" data-price="<?php echo esc_attr($proposal_price); ?>" data-time="<?php echo esc_attr($proposal_time); ?>" data-time-type="<?php echo esc_attr($proposal_time_type); ?>" href="#"><?php esc_html_e('Payment Redirect To Admin', 'felan-framework') ?></a>
                                                    </li>
                                                <?php break;
                                                case 'canceled': ?>
                                                    <li><a class="btn-order-refund" order-id="<?php echo esc_attr($id); ?>" href="#form-project-order-refund"><?php esc_html_e('Refund', 'felan-framework') ?></a>
                                                    </li>
                                                <?php break;
                                                case 'expired': ?>
                                                    <li><a class="btn-completed" order-id="<?php echo esc_attr($id); ?>" href="#"><?php esc_html_e('Complete', 'felan-framework') ?></a>
                                                    </li>
                                                    <li><a class="btn-order-refund" order-id="<?php echo esc_attr($id); ?>" href="#form-project-order-refund"><?php esc_html_e('Refund', 'felan-framework') ?></a>
                                                    </li>
                                                <?php break;
                                                case 'refund': ?>
                                                    <?php if (!empty($project_refund_content)) : ?>
                                                        <li><a class="btn-view-reason" order-id="<?php echo esc_attr($id); ?>" data-content-refund="<?php echo $project_refund_content; ?>" href="#form-project-view-reason"><?php esc_html_e('View reason', 'felan-framework') ?></a>
                                                        </li>
                                                    <?php else : ?>
                                                        <li><a class="btn-add-to-message" href="#" data-text="<?php echo esc_attr('Refund reason text is empty'); ?>"><?php esc_html_e('View reason', 'felan-framework'); ?></a>
                                                        </li>
                                                    <?php endif; ?>
                                            <?php break;
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
        </div>
    <?php } else { ?>
        <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
    <?php } ?>
    <?php $total_post = $data_applicants->found_posts;
    if ($total_post > $posts_per_page && !empty($project_employer_id)) { ?>
        <div class="pagination-dashboard">
            <?php $max_num_pages = $data_applicants->max_num_pages;
            felan_get_template('global/pagination.php', array(
                'total_post' => $total_post,
                'max_num_pages' => $max_num_pages,
                'type' => 'dashboard',
                'layout' => 'number'
            ));
            wp_reset_postdata(); ?>
        </div>
    <?php } ?>
    <input type="hidden" name="mettings_action" value="<?php echo esc_attr($action) ?>" />
</div>