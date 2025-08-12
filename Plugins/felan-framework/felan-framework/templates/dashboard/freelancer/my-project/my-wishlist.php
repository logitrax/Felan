<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
    felan_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'project-my-wishlist');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'project-my-wishlist',
    'felan_project_my_wishlist_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'not_project' => esc_html__('No project found', 'felan-framework'),
    )
);

global $current_user;
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
$my_wishlist = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'project_wishlist', true);
$posts_per_page = 10;

if (empty($my_wishlist)) {
    $my_wishlist = array(0);
}
$args = array(
    'post_type' => 'project',
    'post__in' => $my_wishlist,
    'ignore_sticky_posts' => 1,
    'posts_per_page' => $posts_per_page,
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
);
$data = new WP_Query($args);
?>

<div class="felan-project-my-wishlist entry-my-page">
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="action-search">
                <input class="project-search-control" type="text" name="project_search" placeholder="<?php esc_attr_e('Search project title', 'felan-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'felan-framework') ?></label>
            <div class="select2-field">
                <select class="search-control action-sorting felan-select2" name="project_sort_by">
                    <option value="newest"><?php esc_html_e('Newest', 'felan-framework') ?></option>
                    <option value="oldest"><?php esc_html_e('Oldest', 'felan-framework') ?></option>
                </select>
            </div>
        </div>
    </div>
    <?php if ($data->have_posts()) { ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard" id="project-my-wishlist">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Project Title', 'felan-framework') ?></th>
                        <th><?php esc_html_e('EMPLOYER', 'felan-framework') ?></th>
                        <th><?php esc_html_e('Posted Date', 'felan-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data->have_posts()) : $data->the_post(); ?>
                        <?php
                        $project_id = get_the_ID();
                        global $current_user;
                        wp_get_current_user();
                        $user_id = $current_user->ID;
                        $project_skills = get_the_terms($project_id, 'project-skills');
                        $project_categories = get_the_terms($project_id, 'project-categories');
                        $project_location = get_the_terms($project_id, 'felan_location');
                        $thumbnail = get_the_post_thumbnail_url($project_id, '70x70');
                        $author_id = get_post_field('post_author', $project_id);
                        $author_name = get_the_author_meta('display_name', $author_id);
                        $public_date = get_the_date(get_option('date_format'));
                        ?>
                        <tr>
                            <td>
                                <div class="project-header">
                                    <?php if (!empty($thumbnail)) : ?>
                                        <img class="thumbnail" src="<?php echo $thumbnail; ?>" alt="" />
                                    <?php endif; ?>
                                    <div class="info-project">
                                        <h3 class="title-project-dashboard">
                                            <a href="<?php echo get_the_permalink($project_id); ?>">
                                                <?php echo get_the_title($project_id); ?>
                                            </a>
                                        </h3>
                                        <p>
                                            <?php if (is_array($project_categories)) {
                                                foreach ($project_categories as $categories) { ?>
                                                    <span class="cate"><?php esc_html_e($categories->name); ?></span>
                                            <?php }
                                            } ?>
                                            <?php if (is_array($project_skills)) {
                                                foreach ($project_skills as $skills) { ?>
                                                    <?php esc_html_e('/ ' . $skills->name); ?>
                                            <?php }
                                            } ?>
                                            <?php if (is_array($project_location)) {
                                                foreach ($project_location as $location) { ?>
                                                    <?php esc_html_e('/ ' . $location->name); ?>
                                            <?php }
                                            } ?>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="author">
                                <?php echo $author_name; ?>
                            </td>
                            <td class="table-time">
                                <span class="start-time"><?php echo $public_date ?></span>
                            </td>
                            <?php
                            ?>
                            <td class="action-setting project-control">
                                <a href="#" class="icon-setting"><i class="far fa-ellipsis-h"></i></a>
                                <ul class="action-dropdown">
                                    <?php if ($user_demo == 'yes') : ?>
                                        <li><a class="btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                                    <?php else : ?>
                                        <li><a class="btn-delete" project-id="<?php echo esc_attr($project_id); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a></li>
                                    <?php endif; ?>
                                </ul>
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