<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_user_logged_in()) {
    felan_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'follow-freelancer');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'follow-freelancer',
    'felan_follow_freelancer_vars',
    array(
        'ajax_url'    => FELAN_AJAX_URL,
        'not_freelancer'   => esc_html__('No freelancer found', 'felan-framework'),
    )
);

global $current_user;
$user_id = $current_user->ID;
$follow_freelancer    = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'follow_freelancer', true);
$posts_per_page = 10;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);

if (empty($follow_freelancer)) {
    $follow_freelancer = array(0);
}
$args = array(
    'post_type'           => 'freelancer',
    'post__in'            => $follow_freelancer,
    'ignore_sticky_posts' => 1,
    'posts_per_page'      => $posts_per_page,
    'offset'              => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
);
$data_freelancer = new WP_Query($args);
?>

<div class="felan-follow-freelancer">
    <div class="search-dashboard-warpper">
        <div class="search-left">
            <div class="action-search">
                <input class="search-control" type="text" name="freelancer_search" placeholder="<?php esc_attr_e('Find by name', 'felan-framework') ?>">
                <button class="btn-search">
                    <i class="far fa-search"></i>
                </button>
            </div>
        </div>
        <div class="search-right">
            <label class="text-sorting"><?php esc_html_e('Sort by', 'felan-framework') ?></label>
            <div class="select2-field">
                <select class="search-control action-sorting felan-select2" name="freelancer_sort_by">
                    <option value="newest"><?php esc_html_e('Newest', 'felan-framework') ?></option>
                    <option value="oldest"><?php esc_html_e('Oldest', 'felan-framework') ?></option>
                </select>
            </div>
        </div>
    </div>
    <?php if ($data_freelancer->have_posts()) { ?>
        <div class="table-dashboard-wapper">
            <table class="table-dashboard" id="follow-freelancer">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Name', 'felan-framework') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($data_freelancer->have_posts()) : $data_freelancer->the_post(); ?>
                        <?php
                        global $post;
                        $author_id = $post->post_author;
                        $id = get_the_ID();
                        $freelancer_current_position   = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_current_position', true);
                        $freelancer_locations = get_the_terms($id, 'felan_location');
                        $freelancer_email = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_email', true);
                        $freelancer_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
                        $freelancer_featured = get_post_meta($id, FELAN_METABOX_PREFIX . 'freelancer_featured', true);
                        ?>
                        <tr>
                            <td class="info-user">
                                <?php if (!empty($freelancer_avatar)) : ?>
                                    <img class="image-freelancers" src="<?php echo esc_url($freelancer_avatar) ?>" alt="" />
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
                                                <?php esc_html_e($freelancer_current_position . ' / '); ?>
                                            </div>
                                        <?php } ?>
                                        <?php felan_get_salary_freelancer($id, '-'); ?>
                                        <?php if (is_array($freelancer_locations)) {
                                            foreach ($freelancer_locations as $location) { ?>
                                                <?php esc_html_e(' / ' . $location->name); ?>
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
                                        <a href="#" class="action btn-delete tooltip" items-id="<?php echo esc_attr($id); ?>" data-title="<?php echo esc_attr('Delete', 'felan-framework') ?>"><i class="far fa-trash-alt"></i></a>
                                    <?php endif; ?>

                                </div>
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
    <?php $total_post = $data_freelancer->found_posts;
    if ($total_post > $posts_per_page) { ?>
        <div class="pagination-dashboard">
            <?php $max_num_pages = $data_freelancer->max_num_pages;
            felan_get_template('global/pagination.php', array('total_post' => $total_post, 'max_num_pages' => $max_num_pages, 'type' => 'dashboard', 'layout' => 'number'));
            wp_reset_postdata(); ?>
        </div>
    <?php } ?>
</div>