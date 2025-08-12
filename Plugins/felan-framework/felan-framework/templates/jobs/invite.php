<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$freelancer_id = get_the_ID();
$author_id = get_post_field('post_author', $freelancer_id);
$args = array(
    'posts_per_page' => -1,
    'post_type' => 'jobs',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'author' => $user_id,
    'meta_query' => array(
        array(
            'key' => FELAN_METABOX_PREFIX . 'enable_jobs_package_expires',
            'value' => 0,
            'compare' => '=='
        )
    ),
);
$the_query = new WP_Query($args);
$my_invite = get_user_meta($author_id, FELAN_METABOX_PREFIX . 'my_invite', true);
?>

<div class="form-popup felan-form-invite" id="form-invite-popup">
    <div class="bg-overlay"></div>
    <form class="invite-popup custom-scrollbar">
        <a href="#" class="btn-close"><i class="far fa-times"></i></a>
        <h5><?php esc_html_e('Invite to apply job', 'felan-framework'); ?></h5>
        <p><?php esc_html_e('Select job to invite this user', 'felan-framework'); ?></p>
        <?php
        $list_jobs = array();
        if ($the_query->have_posts()) { ?>
            <ul class="type filter-control custom-scrollbar">
                <?php while ($the_query->have_posts()) :
                    $the_query->the_post();
                    $css_class = '';
                    $key = false;
                    $jobs_id = get_the_ID();
                    $list_jobs[] = get_the_ID();
                    $jobs_title = get_the_title($jobs_id);
                    if (!empty($my_invite)) {
                        $key = array_search($jobs_id, $my_invite);
                    }
                    if ($key !== false) {
                        $css_class = 'checked';
                    }
                ?>
                    <li>
                        <input type="checkbox" id="felan_<?php echo esc_attr($jobs_id); ?>" class="custom-checkbox input-control" name="jobs_invite[]" <?php echo esc_attr($css_class); ?> value="<?php echo esc_attr($jobs_id); ?>">
                        <label for="felan_<?php echo esc_attr($jobs_id); ?>"><?php esc_html_e($jobs_title); ?></label>
                    </li>
                <?php endwhile; ?>
            </ul>
            <div class="button-warpper">
                <a href="#" class="felan-button button-outline button-block felan-clear-invite"><?php esc_html_e('Clear', 'felan-framework'); ?></a>
                <a href="#" class="felan-button button-block" id="btn-saved-invite">
                    <?php esc_html_e('Invite', 'felan-framework'); ?>
                    <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
                </a>
            </div>
            <input type="hidden" name="freelancer_id" value="<?php echo esc_attr($freelancer_id) ?>" />
            <input type="hidden" name="author_id" value="<?php echo esc_attr($author_id) ?>" />
            <input type="hidden" name="list_jobs" value="<?php echo esc_attr(json_encode($list_jobs)) ?>" />
        <?php } else { ?>
            <div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
        <?php } ?>
    </form>
</div>