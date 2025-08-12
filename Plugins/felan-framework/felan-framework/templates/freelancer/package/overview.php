<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
$user_id = $current_user->ID;
$freelancer_package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'freelancer_package_id', $user_id);
$enable_package_service_unlimited_time = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited_time', true);
$enable_package_service_unlimited = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_unlimited', true);
$enable_package_service_featured_unlimited = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_service_featured_unlimited', true);
$freelancer_package_number_service = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service', true);
$freelancer_package_number_service_featured = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_service_featured', true);
$freelancer_package_additional = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_additional_details', true);
if ($freelancer_package_additional > 0) {
    $freelancer_package_additional_text = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'freelancer_package_details_text', true);
}
$field_package = array('jobs_apply','project_apply', 'jobs_wishlist', 'company_follow', 'contact_company', 'info_company', 'send_message', 'review_and_commnent');
?>
<ul class="felan-overview-package">
    <li>
        <span class="name"><?php esc_html_e('Number of services', 'felan-framework') ?></span>
        <span class="content">
            <?php if ($enable_package_service_unlimited == 1) {
                esc_html_e('Unlimited', 'felan-framework');
            } else {
                echo $freelancer_package_number_service;
            } ?>
        </span>
    </li>
    <li>
        <span class="name"><?php esc_html_e('Featured Services', 'felan-framework') ?></span>
        <span class="content">
            <?php if ($enable_package_service_featured_unlimited == 1) {
                esc_html_e('Unlimited', 'felan-framework');
            } else {
                echo $freelancer_package_number_service_featured;
            } ?>
        </span>
    </li>
    <?php foreach ($field_package as $field) :
        $show_option = felan_get_option('enable_freelancer_package_' . $field);
        $show_field = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'show_package_' . $field, true);
        $field_unlimited = get_post_meta($freelancer_package_id, FELAN_METABOX_PREFIX . 'enable_package_' . $field . '_unlimited', true);
        $field_number = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_package_number_' . $field, true);
        if ($field_number === '-1') {
            $field_number = 0;
        }
        $is_check = false;
        switch ($field) {
            case 'jobs_apply':
                $name = esc_html__('Number of job applications', 'felan-framework');
                break;
            case 'jobs_wishlist':
                $name = esc_html__('Number of jobs wishlist', 'felan-framework');
                break;
            case 'company_follow':
                $name = esc_html__('Number of companies followed', 'felan-framework');
                break;
            case 'contact_company':
                $name = esc_html__('View company in job details', 'felan-framework');
                $is_check = true;
                break;
            case 'info_company':
                $name = esc_html__('View company information', 'felan-framework');
                $is_check = true;
                break;
            case 'send_message':
                $name = esc_html__('Send Messages', 'felan-framework');
                $is_check = true;
                break;
            case 'review_and_commnent':
                $name = esc_html__('Review and comment', 'felan-framework');
                $is_check = true;
                break;
            case 'project_apply':
                $name = esc_html__('Number of proposals submitted', 'felan-framework');
                break;
        }
        if ($show_field == 1 && $show_option == 1) :
    ?>
            <li>
                <span class="name"><?php echo $name; ?></span>
                <span class="content">
                    <?php if ($is_check == true) { ?>
                        <i class="far fa-check"></i>
                    <?php } else { ?>
                        <?php if ($field_unlimited == 1) { ?>
                            <?php esc_html_e('Unlimited', 'felan-framework'); ?>
                        <?php } else { ?>
                            <?php echo $field_number; ?>
                        <?php } ?>
                    <?php } ?>
                </span>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($freelancer_package_additional > 0) :
        foreach ($freelancer_package_additional_text as $additional) : ?>
            <?php if (!empty($additional)) : ?>
                <li class="list-group-item">
                    <span class="name"><?php echo $additional; ?></span>
                    <span class="content">
                        <i class="far fa-check"></i>
                    </span>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

</ul>