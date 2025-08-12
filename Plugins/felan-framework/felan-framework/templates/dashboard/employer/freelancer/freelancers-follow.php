<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
    felan_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancers-dashboard');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'freelancers-dashboard',
    'felan_freelancers_dashboard_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_freelancers' => esc_html__('No freelancers found', 'felan-framework'),
    )
);
global $current_user;
$user_id = $current_user->ID;
$id = get_the_ID();
$posts_per_page = 10;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);

$args_company = array(
    'post_type' => 'company',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'author' => $user_id,
);
$data_company = new WP_Query($args_company);
$jobs_company_id = array();
if ($data_company->have_posts()) {
    while ($data_company->have_posts()) : $data_company->the_post();
        $jobs_company_id[] = get_the_ID();
    endwhile;
}
$users_freelancers = get_users(array('role__in' => array('felan_user_freelancer')));
$user_follow = $company = array();
foreach ($users_freelancers as $user) {
    $my_follow = get_user_meta($user->ID, FELAN_METABOX_PREFIX . 'my_follow', true);
    if (is_array($my_follow)) {
        $check_company = array_intersect($my_follow, $jobs_company_id);
        if (is_array($check_company) && !empty($check_company)) {
            array_push($user_follow, $user->ID);
            array_push($company, $check_company);
        }
    }
}
$user_follow_company = array_combine($user_follow, $company);
$user_follow = implode(',', $user_follow);
$args_freelancers = array(
    'post_type' => 'freelancer',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'author' => $user_follow,
);
$data_freelancers = new WP_Query($args_freelancers);
$freelancers_id = array();
if ($data_freelancers->have_posts()) {
    while ($data_freelancers->have_posts()) : $data_freelancers->the_post();
        $freelancers_id[] = get_the_ID();
    endwhile;
}
$args = array(
    'post_type' => 'freelancer',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
);
if (!empty($user_follow)) {
    $args['post__in'] = $freelancers_id;
}
$data = new WP_Query($args);

?>
<div class="freelancers-dashboard">
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="action-search">
                <input class="search-control" type="text" name="freelancers_search" placeholder="<?php esc_attr_e('Find by name', 'felan-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'felan-framework') ?></label>
            <div class="select2-field">
                <select class="search-control action-sorting felan-select2" name="freelancers_sort_by">
                    <option value="newest"><?php esc_html_e('Newest', 'felan-framework') ?></option>
                    <option value="oldest"><?php esc_html_e('Oldest', 'felan-framework') ?></option>
                </select>
            </div>
        </div>
    </div>
    <?php if ($data->have_posts() && !empty($freelancers_id) && !empty($user_follow)) { ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard" id="freelancers-db">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Name', 'felan-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $list_id_freelancers = array();
                    while ($data->have_posts()) : $data->the_post(); ?>
                        <?php
                        global $post;
                        $author_id = $post->post_author;
                        $id = get_the_ID();
                        $list_id_freelancers[] = $id;
                        $freelancer_current_position   = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);
                        $freelancer_locations = get_the_terms($id, 'felan_location');
                        $freelancer_email = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_email', true);
                        $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                        $freelancer_featured = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_featured', true);
                        $user_follow_athour = '';
                        if (!empty($user_follow_company[$author_id])) {
                            $user_follow_athour = implode(',', $user_follow_company[$author_id]);
                        }
                        ?>
                        <tr>
                            <td class="info-user">
                                <?php if (!empty($freelancer_avatar)) : ?>
                                    <img class="image-freelancers" src="<?php echo esc_attr($freelancer_avatar) ?>" alt="" />
                                <?php else : ?>
                                    <div class="image-freelancers"><i class="far fa-camera"></i></div>
                                <?php endif; ?>
                                <div class="info-details">
                                    <h3>
                                        <a href="<?php echo esc_url(get_the_permalink($id)); ?>"><?php esc_html_e(get_the_title($id)); ?></a>
                                        <?php if ($freelancer_featured == 1) : ?>
                                            <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'felan-framework') ?>"><i class="far fa-check"></i></span>
                                        <?php endif; ?>
                                    </h3>
                                    <div class="cate-info">
                                        <?php if (!empty($freelancer_current_position)) { ?>
                                            <div class="freelancer-current-position">
                                                <?php esc_html_e($freelancer_current_position . ' /'); ?>
                                            </div>
                                        <?php } ?>
                                        <?php felan_get_salary_freelancer($id, '-'); ?>
                                        <?php if (is_array($freelancer_locations)) {
                                            foreach ($freelancer_locations as $location) { ?>
                                                <?php esc_html_e('/ ' . $location->name); ?>
                                        <?php }
                                        } ?>
                                    </div>
                                </div>
                            </td>
                            <td class="action-setting">
                                <div class="list-action">
                                    <a href="<?php echo esc_url(get_the_permalink($id)); ?>" target="_blank" class="action icon-view tooltip" data-title="<?php echo esc_attr('View', 'felan-framework') ?>"><i class="far fa-eye"></i></i></a>
                                    <a href="mailto: <?php esc_html_e($freelancer_email); ?>" class="action icon-gmail tooltip" data-title="<?php echo esc_attr('Send Email', 'felan-framework') ?>"><i class="far fa-envelope-open-text"></i></a>

                                    <?php if ($user_demo == 'yes') : ?>
                                        <a href="#" class="btn-add-to-message action tooltip" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" data-title="<?php echo esc_attr('Delete', 'felan-framework') ?>"><i class="far fa-trash-alt"></i></a>
                                        </a>
                                    <?php else : ?>
                                        <a href="#" class="action btn-delete tooltip" athour-id="<?php echo esc_attr($author_id) ?>" follow_company="<?php echo $user_follow_athour; ?>" items-id="<?php echo esc_attr($id); ?>" data-title="<?php echo esc_attr('Delete', 'felan-framework') ?>"><i class="far fa-trash-alt"></i></a>
                                    <?php endif; ?>

                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <input type="hidden" name="freelancers_id" value="<?php echo implode(',', $freelancers_id); ?>">
            </table>
            <div class="felan-loading-effect"><span class="felan-dual-ring"></span></div>
        </div>
    <?php } else { ?>
        <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
    <?php } ?>
    <?php $total_post = $data->found_posts;
    if ($total_post > $posts_per_page && !empty($user_follow)) { ?>
        <div class="pagination-dashboard">
            <?php $max_num_pages = $data->max_num_pages;
            felan_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
            wp_reset_postdata(); ?>
        </div>
    <?php } ?>
</div>