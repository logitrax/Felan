<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer-follow');

$key = false;
$user_id = $current_user->ID;
$follow_freelancer = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'follow_freelancer', true);
$id = get_the_ID();
if (!empty($freelancer_id)) {
    $id = $freelancer_id;
}
$author_id = get_post_field('post_author', $id);

if (!empty($follow_freelancer)) {
    $key = array_search($id, $follow_freelancer);
}

$css_class = '';
if ($key !== false) {
    $css_class = 'added';
}

$paid_submission_type = felan_get_option('paid_submission_type');
$package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
$felan_profile = new Felan_Profile();
$check_package = $felan_profile->user_package_available($user_id);
$show_package_follow = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'show_package_company_freelancer_follow', true);
$enable_company_package_follow = felan_get_option('enable_company_package_freelancer_follow');
$company_package_number_freelancer_follow = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_freelancer_follow', true);
?>
<?php if (is_user_logged_in() && in_array('felan_user_employer', (array)$current_user->roles)) { ?>
    <?php if($user_id == $author_id) { ?>
        <a href="#" class="btn-add-to-message add-follow-freelancer tooltip <?php echo esc_attr($css_class); ?>"
           data-title="<?php echo esc_attr__('Save', 'felan-framework') ?>"
           data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
            <span class="icon-plus"><i class="far fa-heart"></i></span>
            <span class="text-icon"><?php echo esc_html('Wishlist','felan-framework');?></span>
        </a>
    <?php } else { ?>
        <?php if ($paid_submission_type == 'per_package' && $enable_company_package_follow === '1' && ($show_package_follow === '0' || $company_package_number_freelancer_follow <= 0 || ($check_package == -1 || $check_package == 0))) { ?>
            <a href="#" class="btn-add-to-message add-follow-freelancer tooltip <?php echo esc_attr($css_class); ?>"
               data-title="<?php echo esc_attr__('Save', 'felan-framework') ?>"
               data-text="<?php echo esc_attr('The quantity in your package has reached its limit or your package has expired', 'felan-framework'); ?>">
                <span class="icon-plus"><i class="far fa-heart"></i></span>
                <span class="text-icon"><?php echo esc_html('Wishlist','felan-framework');?></span>
            </a>
        <?php } else { ?>
            <a href="#" class="felan-add-to-follow-freelancer add-follow-freelancer tooltip <?php echo esc_attr($css_class); ?>"
               data-title="<?php echo esc_attr__('Save', 'felan-framework') ?>"
               data-freelancer-id="<?php echo intval($id) ?>">
                <?php if ($key !== false) { ?>
                    <span class="icon-plus"><i class="fas fa-heart"></i></span>
                    <span class="text-icon"><?php echo esc_html('Wishlist','felan-framework');?></span>
                <?php } else { ?>
                    <span class="icon-plus"><i class="far fa-heart"></i></span>
                    <span class="text-icon"><?php echo esc_html('Wishlist','felan-framework');?></span>
                <?php } ?>
            </a>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <div class="logged-out">
        <a href="#popup-form" class="btn-login notice-employer add-follow-freelancer tooltip <?php echo esc_attr($css_class); ?>"
           data-title="<?php echo esc_attr__('Save', 'felan-framework') ?>"
           data-freelancer-id="<?php echo intval($id) ?>"
           data-notice="<?php esc_attr_e('Please login role Employer to view', 'felan-framework') ?>">
            <span class="icon-plus">
                <i class="far fa-heart"></i>
                <span class="text-icon"><?php echo esc_html('Wishlist','felan-framework');?></span>
            </span>
        </a>
    </div>
<?php } ?>