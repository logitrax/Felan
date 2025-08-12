<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
wp_get_current_user();
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'service-wishlist');

$key = false;
$user_id = $current_user->ID;
$service_wishlist = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'service_wishlist', true);
$id = get_the_ID();
$author_id = get_post_field('post_author', $id);

if (!empty($service_id)) {
    $id = $service_id;
}

if (!empty($service_wishlist)) {
    $key = array_search($id, $service_wishlist);
}

$css_class = '';
if ($key !== false) {
    $css_class = 'added';
}
?>
<?php if (is_user_logged_in() && in_array('felan_user_employer', (array)$current_user->roles)) { ?>
    <?php if($user_id == $author_id) { ?>
        <a href="#" class="btn-add-to-message btn-add-to-wishlist tooltip"
           data-title="<?php esc_attr_e('Wishlist', 'felan-framework') ?>"
           data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
            <span class="icon-heart">
                <i class="far fa-heart"></i>
            </span>
        </a>
    <?php } else { ?>
        <a href="#" class="felan-service-wishlist btn-add-to-wishlist tooltip <?php echo esc_attr($css_class); ?>" data-title="<?php esc_attr_e('Wishlist', 'felan-framework') ?>" data-service-id="<?php echo intval($id) ?>">
        <span class="icon-heart">
            <i class="far fa-heart"></i>
        </span>
        </a>
    <?php } ?>
<?php } else { ?>
    <div class="logged-out">
        <a href="#popup-form" class="btn-login btn-add-to-wishlist tooltip notice-employer <?php echo esc_attr($css_class); ?>" data-title="<?php esc_attr_e('Wishlist', 'felan-framework') ?>" data-service-id="<?php echo intval($id) ?>" data-notice="<?php esc_attr_e('Please login Employer', 'felan-framework') ?>">
            <span class="icon-heart">
                <i class="far fa-heart"></i>
            </span>
        </a>
    </div>
<?php } ?>