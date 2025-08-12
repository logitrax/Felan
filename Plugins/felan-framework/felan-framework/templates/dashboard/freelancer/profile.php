<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script('plupload');
wp_enqueue_script('jquery-validate');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer-submit');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer');
$custom_field_freelancer = felan_render_custom_field('freelancer');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'freelancer-submit',
    'felan_freelancer_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'site_url' => get_site_url(),
        'text_present' => esc_attr__('Present', 'felan-framework'),
        'custom_field_freelancer' => $custom_field_freelancer,
    )
);

global $current_user, $hide_freelancer_fields, $hide_freelancer_group_fields, $freelancer_data, $freelancer_meta_data;
wp_get_current_user();
$freelancer_id = felan_get_post_id_freelancer();
if (!empty($freelancer_id)) {
    $freelancer_data = get_post($freelancer_id);
    $freelancer_meta_data = get_post_custom($freelancer_data->ID);
}
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);

$ajax_url = admin_url('admin-ajax.php');
$upload_nonce = wp_create_nonce('freelancer_allow_upload');

$profile_strength_percent = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_profile_strength']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_profile_strength'][0] : '';
if (empty($profile_strength_percent)) {
    $profile_strength_percent = 0;
}

$hide_freelancer_fields = felan_get_option('hide_freelancer_fields', array());
if (!is_array($hide_freelancer_fields)) {
    $hide_freelancer_fields = array();
}
$hide_freelancer_group_fields = felan_get_option('hide_freelancer_group_fields', array());
if (!is_array($hide_freelancer_group_fields)) {
    $hide_freelancer_group_fields = array();
}
$layout = apply_filters('felan/dashboard/freelancer/profile/layout', array('info', 'education', 'experience', 'skills', 'projects', 'awards'));
?>

<div id="freelancer-profile" class="freelancer-profile">

    <div class="entry-my-page freelancer-profile-dashboard">

        <div class="entry-title">
            <h4><?php esc_html_e('Profile Settings', 'felan-framework') ?></h4>
        </div>

        <div class="tab-dashboard">
            <ul class="tab-list freelancer-profile-tab">
                <?php foreach ($layout as $value) {
                    switch ($value) {
                        case 'info':
                            $name = esc_html__('Basic Info', 'felan-framework');
                            $class = '';
                            break;
                        case 'education':
                            $name = esc_html__('Education', 'felan-framework');
                            $class = 'repeater';
                            break;
                        case 'experience':
                            $name = esc_html__('Experience', 'felan-framework');
                            $class = 'repeater';
                            break;
                        case 'skills':
                            $name = esc_html__('Skills', 'felan-framework');
                            $class = '';
                            break;
                        case 'projects':
                            $name = esc_html__('Portfolio', 'felan-framework');
                            $class = 'repeater ';
                            break;
                        case 'awards':
                            $name = esc_html__('Awards', 'felan-framework');
                            $class = 'repeater';
                            break;
                    }
                    if (!in_array($value, $hide_freelancer_group_fields)) : ?>
                        <li class="tab-item <?php esc_attr_e($class); ?>"><a href="#tab-<?php esc_attr_e($value) ?>"><?php esc_html_e($name) ?></a>
                        </li>
                <?php endif;
                } ?>

                <?php $custom_field_freelancer = felan_render_custom_field('freelancer');
                if (count($custom_field_freelancer) > 0) :
                    $tabs_array = array();
                    foreach ($custom_field_freelancer as $field) {
                        if ((!in_array($field['section'], $tabs_array)) && !empty($field['section'])) {
                            $tabs_array[] = $field['section'];
                        }
                    }
                    foreach ($tabs_array as $value) {
                        $tabs_id = str_replace(" ", "-", $value); ?>
                        <li class="tab-item"><a href="#tab-<?php echo $tabs_id ?>"><?php echo $value; ?></a></li>
                    <?php } ?>
                <?php endif; ?>
            </ul>

            <div class="tab-content row">
                <form action="#" method="post" enctype="multipart/form-data" id="freelancer-profile-form" class="freelancer-profile-form form-dashboard  col-lg-8 col-md-7">
                    <input type="hidden" name="freelancer_profile_strength" value="<?php esc_attr_e($profile_strength_percent) ?>">
                    <?php foreach ($layout as $value) {
                        switch ($value) {
                            case 'info':
                                break;
                            case 'education':
                                break;
                            case 'experience':
                                break;
                            case 'skills':
                                break;
                            case 'projects':
                                break;
                            case 'awards':
                                break;
                        }
                        if (!in_array($value, $hide_freelancer_group_fields)) : ?>
                            <?php felan_get_template('dashboard/freelancer/profile/' . $value . '.php'); ?>
                    <?php endif;
                    } ?>

                    <?php $custom_field_freelancer = felan_render_custom_field('freelancer');

                    if (count($custom_field_freelancer) > 0) :
                        $sections = [];
                        foreach ($custom_field_freelancer as $field) {
                            if (!empty($field['section'])) {
                                if (in_array($field['section'], $sections)) {
                                    continue;
                                }

                                $sections[] = $field['section'];
                                $tabs_id = str_replace(" ", "-", $field['section']); ?>
                                <div id="tab-<?php echo $tabs_id; ?>" class="tab-info block-from">
                                    <h5><?php echo $field['section']; ?></h5>
                                    <?php felan_custom_field_freelancer($field['section'], true); ?>
                                </div>
                        <?php }
                        } ?>
                    <?php endif; ?>

                    <div class="button-warpper">
                        <a href="<?php echo felan_get_permalink('freelancer_dashboard'); ?>" class="felan-button button-outline">
                            <?php esc_html_e('Cancel', 'felan-framework') ?>
                        </a>
                        <?php if ($user_demo == 'yes') : ?>
                            <button class="felan-button btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>">
                                <span><?php esc_html_e('Publish', 'felan-framework'); ?></span>
                            </button>
                        <?php else : ?>
                            <button type="submit" class="btn-update-profile felan-button" name="submit_profile">
                                <span><?php esc_html_e('Publish', 'felan-framework'); ?></span>
                                <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </form>

                <div class="freelancer-profile-strength col-lg-4 col-md-5">
                    <div class="has-sticky">
                        <div class="profile-strength tip" style="--pct: <?php echo esc_attr($profile_strength_percent) ?>">
                            <h1><span><?php echo esc_attr($profile_strength_percent) ?></span><span>%</span></h1>
                            <div><?php esc_html_e('Profile Strength', 'felan-framework') ?></div>
                            <div class="tip-content post-bottom">
                                <ul class="profile-list-check">
                                    <?php foreach ($layout as $value) {
                                        switch ($value) {
                                            case 'info':
                                                $name = esc_html__('Basic Info', 'felan-framework');
                                                break;
                                            case 'education':
                                                $name = esc_html__('Education', 'felan-framework');
                                                break;
                                            case 'experience':
                                                $name = esc_html__('Experience', 'felan-framework');
                                                break;
                                            case 'skills':
                                                $name = esc_html__('Skills', 'felan-framework');
                                                break;
                                            case 'projects':
                                                $name = esc_html__('Projects', 'felan-framework');
                                                break;
                                            case 'awards':
                                                $name = esc_html__('Awards', 'felan-framework');
                                                break;
                                        }
                                        if (!in_array($value, $hide_freelancer_group_fields)) : ?>
                                            <li class="profile-check-item" id="<?php echo 'profile-check-' . $value ?>" data-has-check="<?php echo sprintf(__('%s has enough information', 'felan-framework'), $name); ?>" data-not-check="<?php echo sprintf(__('%s not enough information', 'felan-framework'), $name); ?>">
                                                <i class="far fa-check"></i>
                                                <span><?php echo sprintf(__('%s not enough information', 'felan-framework'), $name); ?></span>
                                            </li>
                                    <?php endif;
                                    } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>