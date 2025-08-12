<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
wp_get_current_user();
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'company-follow');

$key          = false;
$user_id      = $current_user->ID;
$my_follow = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'my_follow', true);
$id           = get_the_ID();
if (!empty($company_id)) {
    $id = $company_id;
}
$author_id = get_post_field('post_author', $id);

if (!empty($my_follow)) {
    $key = array_search($id, $my_follow);
}
$css_class = '';
if ($key !== false) {
    $css_class = 'added';
}
$freelancer_paid_submission_type = felan_get_option('freelancer_paid_submission_type');
$check_package = felan_get_field_check_freelancer_package('company_follow');
$freelancer_package_number_company_follow = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_company_follow', true);
?>
<?php if (is_user_logged_in() && in_array('felan_user_freelancer', (array)$current_user->roles)) { ?>
    <?php if($user_id == $author_id) { ?>
        <a href="#" class="btn-add-to-message tooltip"
           data-text="<?php echo esc_attr('This feature is not available for the same user ID', 'felan-framework'); ?>">
            <span class="icon-plus">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.75 3.5C5.12665 3.5 3 5.75956 3 8.54688C3 14.125 12 20.5 12 20.5C12 20.5 21 14.125 21 8.54688C21 5.09375 18.8734 3.5 16.25 3.5C14.39 3.5 12.7796 4.63593 12 6.2905C11.2204 4.63593 9.61003 3.5 7.75 3.5Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </a>
    <?php } else { ?>
        <?php if ($check_package == -1 || $check_package == 0 || ($freelancer_paid_submission_type == 'freelancer_per_package' && $freelancer_package_number_company_follow < 1)) { ?>
            <a href="#" class="btn-add-to-message tooltip <?php echo esc_attr($css_class); ?>" data-text="<?php echo esc_attr('The quantity in your package has reached its limit or your package has expired', 'felan-framework'); ?>">
            <span class="icon-plus">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.75 3.5C5.12665 3.5 3 5.75956 3 8.54688C3 14.125 12 20.5 12 20.5C12 20.5 21 14.125 21 8.54688C21 5.09375 18.8734 3.5 16.25 3.5C14.39 3.5 12.7796 4.63593 12 6.2905C11.2204 4.63593 9.61003 3.5 7.75 3.5Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            </a>
        <?php } else { ?>
            <a href="#" class="button-outline-accent felan-add-to-follow add-follow-company <?php echo esc_attr($css_class); ?>" data-company-id="<?php echo intval($id) ?>">
            <span class="icon-plus">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.75 3.5C5.12665 3.5 3 5.75956 3 8.54688C3 14.125 12 20.5 12 20.5C12 20.5 21 14.125 21 8.54688C21 5.09375 18.8734 3.5 16.25 3.5C14.39 3.5 12.7796 4.63593 12 6.2905C11.2204 4.63593 9.61003 3.5 7.75 3.5Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            </a>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <div class="account logged-out">
        <a href="#popup-form" class="button-outline-accent btn-login add-follow-company <?php echo esc_attr($css_class); ?>" data-company-id="<?php echo intval($id) ?>">
            <span class="icon-plus">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.75 3.5C5.12665 3.5 3 5.75956 3 8.54688C3 14.125 12 20.5 12 20.5C12 20.5 21 14.125 21 8.54688C21 5.09375 18.8734 3.5 16.25 3.5C14.39 3.5 12.7796 4.63593 12 6.2905C11.2204 4.63593 9.61003 3.5 7.75 3.5Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </a>
    </div>
<?php } ?>