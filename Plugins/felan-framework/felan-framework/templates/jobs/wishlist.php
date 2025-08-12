<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
wp_get_current_user();
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'wishlist');

$key = false;
$user_id = $current_user->ID;
$my_wishlist = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_wishlist', true);
$id = get_the_ID();
$author_id = get_post_field('post_author', $id);
if (!empty($jobs_id)) {
    $id = $jobs_id;
}
if (!empty($my_wishlist)) {
    $key = array_search($id, $my_wishlist);
}
$css_class = '';
if ($key !== false) {
    $css_class = 'added';
}
$freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
$freelancer_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
$check_package = felan_get_field_check_freelancer_package('jobs_wishlist');
$freelancer_package_number_jobs_wishlist = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_jobs_wishlist', true);
?>
<?php if (is_user_logged_in() && in_array('felan_user_freelancer', (array)$current_user->roles)) { ?>
    <?php if($user_id == $author_id) { ?>
        <a href="#" class="btn-add-to-message btn-add-to-wishlist tooltip"
           data-title="<?php esc_attr_e('Wishlist', 'felan-framework') ?>"
           data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
            <span class="icon-heart">
                <i class="far fa-heart"></i>
            </span>
        </a>
    <?php } else { ?>
        <?php if ($check_package == -1 || $check_package == 0 || ($freelancer_paid_submission_type == 'freelancer_per_package' && $freelancer_package_number_jobs_wishlist < 1)) { ?>
            <a href="#" class="btn-add-to-message btn-add-to-wishlist tooltip <?php echo esc_attr($css_class); ?>" data-title="<?php esc_attr_e('Wishlist', 'felan-framework') ?>" data-text="<?php echo esc_attr('The quantity in your package has reached its limit or your package has expired', 'felan-framework'); ?>">
            <span class="icon-heart">
                <i class="far fa-heart"></i>
            </span>
            </a>
        <?php } else { ?>
            <a href="#" class="felan-add-to-wishlist btn-add-to-wishlist tooltip <?php echo esc_attr($css_class); ?>" data-jobs-id="<?php echo intval($id) ?>" data-title="<?php esc_attr_e('Wishlist', 'felan-framework') ?>">
            <span class="icon-heart">
                <i class="far fa-heart"></i>
            </span>
            </a>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <div class="logged-out">
        <a href="#popup-form" class="btn-login btn-add-to-wishlist tooltip <?php echo esc_attr($css_class); ?>" data-jobs-id="<?php echo intval($id) ?>" data-title="<?php esc_attr_e('Wishlist', 'felan-framework') ?>">
            <span class="icon-heart">
                <i class="far fa-heart"></i>
            </span>
        </a>
    </div>
<?php } ?>