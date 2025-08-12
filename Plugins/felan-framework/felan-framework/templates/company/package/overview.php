<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
$user_id = $current_user->ID;
$package_id = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_id', $user_id);
$package_unlimited_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job', true);
$package_unlimited_featured_job = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_job_featured', true);
$package_num_job = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_job', true);
$package_num_featured_job = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_featured', true);

$package_unlimited_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_project', true);
$package_unlimited_featured_project = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_unlimited_project_featured', true);
$package_num_project = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_project', true);
$package_num_featured_project = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_project_featured', true);

$package_additional = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_additional_details', true);
if ($package_additional > 0) {
    $package_additional_text = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'package_details_text', true);
}
$field_package = array('freelancer_follow', 'download_cv', 'invite', 'send_message', 'print', 'review_and_commnent', 'info');
?>
<ul class="felan-overview-package">
    <li>
        <span class="name"><?php esc_html_e('Number of jobs', 'felan-framework') ?></span>
        <span class="content">
            <?php if ($package_unlimited_job == 1) {
                esc_html_e('Unlimited', 'felan-framework');
            } else {
                echo $package_num_job;
            } ?>
        </span>
    </li>
    <?php if (!empty($package_num_featured_job)) : ?>
        <li>
            <span class="name"><?php esc_html_e('Featured jobs', 'felan-framework') ?></span>
            <span class="content">
                <?php if ($package_unlimited_featured_job == 1) {
                    esc_html_e('Unlimited', 'felan-framework');
                } else {
                    echo $package_num_featured_job;
                } ?>
            </span>
        </li>
    <?php endif; ?>
    <li>
        <span class="name"><?php esc_html_e('Number of project', 'felan-framework') ?></span>
        <span class="content">
            <?php if ($package_unlimited_project == 1) {
                esc_html_e('Unlimited', 'felan-framework');
            } else {
                echo $package_num_project;
            } ?>
        </span>
    </li>
    <?php if (!empty($package_num_featured_project)) : ?>
        <li>
            <span class="name"><?php esc_html_e('Featured project', 'felan-framework') ?></span>
            <span class="content">
                <?php if ($package_unlimited_featured_project == 1) {
                    esc_html_e('Unlimited', 'felan-framework');
                } else {
                    echo $package_num_featured_project;
                } ?>
            </span>
        </li>
    <?php endif; ?>
    <?php foreach ($field_package as $field) :
        $show_option = felan_get_option('enable_company_package_' . $field);
        $show_field = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'show_package_company_' . $field, true);
        $field_unlimited = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'enable_package_' . $field . '_unlimited', true);
        $field_number = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'package_number_' . $field, true);
        if ($field_number == '') {
            $field_number = get_post_meta($package_id, FELAN_METABOX_PREFIX . 'company_package_number_' . $field, true);
        } elseif ($field_number === '-1') {
            $field_number = 0;
        }

        $is_check = true;
        switch ($field) {
            case 'freelancer_follow':
                $name = esc_html__('Number of freelancers follow', 'felan-framework');
                $is_check = false;
                break;
            case 'download_cv':
                $name = esc_html__('Download CV', 'felan-framework');
                $is_check = false;
                break;
            case 'invite':
                $name = esc_html__('Invite Freelancers', 'felan-framework');
                break;
            case 'send_message':
                $name = esc_html__('Send Messages', 'felan-framework');
                break;
            case 'print':
                $name = esc_html__('Print freelancer profiles', 'felan-framework');
                break;
            case 'review_and_commnent':
                $name = esc_html__('Review and comment', 'felan-framework');
                break;
            case 'info':
                $name = esc_html__('View freelancer information', 'felan-framework');
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

    <?php if ($package_additional > 0) {
        foreach ($package_additional_text as $value) { ?>
            <li class="list-group-item">
                <span class="name"><?php echo $value; ?></span>
                <span class="content">
                    <i class="far fa-check"></i>
                </span>
            </li>
    <?php }
    } ?>
</ul>