<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_freelancer_fields, $freelancer_data, $freelancer_meta_data, $current_user;
$user_id = $current_user->ID;

$enable_freelancer_language_multiple = felan_get_option('enable_freelancer_language_multiple', '0');
$freelancer_id = felan_get_post_id_freelancer();
$freelancer_des = $freelancer_data->post_content;
$freelancer_first_name = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_first_name']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_first_name'][0] : '';
$freelancer_last_name = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_last_name']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_last_name'][0] : '';
$freelancer_email = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_email']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_email'][0] : '';
$freelancer_current_position = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_current_position']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_current_position'][0] : '';
$freelancer_categories = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_categories']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_categories'][0] : '';
$freelancer_dob = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_dob']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_dob'][0] : '';
$freelancer_age = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_age']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_age'][0] : '';
$freelancer_gender = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_gender']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_gender'][0] : '';
$freelancer_languages = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_languages']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_languages'][0] : '';
$freelancer_qualification = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_qualification']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_qualification'][0] : '';
$freelancer_yoe = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_yoe']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_yoe'][0] : '';
$freelancer_salary_type = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_salary_type']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_salary_type'][0] : '';
$freelancer_offer_salary = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_offer_salary']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_offer_salary'][0] : '';
$freelancer_show_my_profile = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_show_my_profile']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_show_my_profile'][0] : '';

$date_format = get_option('date_format');
$phone_code = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'phone_code', true);
if(empty($phone_code)){
    $phone_code = felan_get_option('default_phone_number');
}
$user_phone = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'author_mobile_number', true);
if ($user_phone) {
    $freelancer_phone = $user_phone;
} else {
    $freelancer_phone = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_phone']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_phone'][0] : '';
}

$freelancer_avatar_id = $user_id;
$freelancer_avatar_url = get_the_author_meta('author_avatar_image_url', $user_id);
$freelancer_cover_image_id = get_post_thumbnail_id($freelancer_data->ID);
$freelancer_cover_image_url = get_the_post_thumbnail_url($freelancer_data->ID, 'full');
$image_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
felan_get_thumbnail_enqueue();
felan_get_avatar_enqueue();

$google_gmail = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'user-google-email', true);
if (!empty($google_gmail)) {
    $freelancer_email = $google_gmail;
} else {
    $freelancer_email = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_email']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_email'][0] : '';
}
?>

<div class="freelancer basic-info block-from">

    <h6><?php esc_html_e('Basic Information', 'felan-framework'); ?></h6>

    <input type="hidden" name="freelancer_id" value="<?php echo esc_attr($freelancer_id) ?>">

    <div class="felan-avatar-freelancer">

        <?php if (!in_array('fields_freelancer_avatar', $hide_freelancer_fields)) : ?>
            <div class="freelancer-fields-avatar felan-fields-avatar">
                <label><?php esc_html_e('Your photo', 'felan-framework'); ?></label>
                <div class="form-field">
                    <div id="felan_avatar_errors" class="errors-log"></div>
                    <div id="felan_avatar_container" class="file-upload-block preview">
                        <div id="felan_avatar_view" data-image-id="<?php echo $freelancer_avatar_id; ?>" data-image-url="<?php if (!empty($freelancer_avatar_url)) {
                                                                                                                                echo $freelancer_avatar_url;
                                                                                                                            } ?>"></div>
                        <div id="felan_add_avatar">
                            <i class="far fa-arrow-from-bottom large"></i>
                            <p id="felan_drop_avatar">
                                <button type="button" id="felan_select_avatar"><?php esc_html_e('Upload', 'felan-framework') ?></button>
                            </p>
                        </div>
                        <input type="hidden" class="avatar_url form-control" name="author_avatar_image_url" value="<?php echo $freelancer_avatar_url; ?>" id="avatar_url">
                        <input type="hidden" class="avatar_id" name="author_avatar_image_id" value="<?php echo $freelancer_avatar_id; ?>" id="avatar_id" />
                    </div>
                </div>
                <div class="field-note"><?php echo sprintf(__('Maximum file size: %s.', 'felan-framework'), $image_max_file_size); ?></div>
            </div>
        <?php endif; ?>

        <?php if (!in_array('fields_freelancer_thumbnail', $hide_freelancer_fields)) : ?>
            <div class="freelancer-fields-thumbnail felan-fields-thumbnail">
                <label><?php esc_html_e('Cover image', 'felan-framework'); ?></label>
                <div class="form-field">
                    <div id="felan_thumbnail_errors" class="errors-log"></div>
                    <div id="felan_thumbnail_container" class="file-upload-block preview">
                        <div id="felan_thumbnail_view" data-image-id="<?php echo $freelancer_cover_image_id; ?>" data-image-url="<?php if (!empty($freelancer_cover_image_url)) {
                                                                                                                                        echo $freelancer_cover_image_url;
                                                                                                                                    } ?>"></div>
                        <div id="felan_add_thumbnail">
                            <i class="far fa-arrow-from-bottom large"></i>
                            <p id="felan_drop_thumbnail">
                                <button type="button" id="felan_select_thumbnail"><?php esc_html_e('Click here', 'felan-framework') ?></button>
                                <?php esc_html_e(' or drop files to upload', 'felan-framework') ?>
                            </p>
                        </div>
                        <input type="hidden" class="thumbnail_url form-control" name="freelancer_cover_image_url" value="<?php echo $freelancer_cover_image_url; ?>" id="thumbnail_url">
                        <input type="hidden" class="thumbnail_id" name="freelancer_cover_image_id" value="<?php echo $freelancer_cover_image_id; ?>" id="thumbnail_id" />
                    </div>
                </div>
                <p class="felan-thumbnail-size"><?php esc_html_e('The cover image size should be max 1920 x 400px', 'felan-framework') ?></p>
            </div>
        <?php endif; ?>
    </div>


    <div class="row">
        <?php if (!in_array('fields_freelancer_first_name', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_first_name"><?php esc_html_e('First name', 'felan-framework') ?></label>
                <input class="point-mark" type="text" id="user_firstname" name="freelancer_first_name" placeholder="<?php esc_attr_e('First name', 'felan-framework') ?>" value="<?php echo esc_attr($freelancer_first_name); ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_last_name', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_last_name"><?php esc_html_e('Last name', 'felan-framework') ?></label>
                <input class="point-mark" type="text" id="user_lastname" name="freelancer_last_name" placeholder="<?php esc_attr_e('Last name', 'felan-framework') ?>" value="<?php echo esc_attr($freelancer_last_name); ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_email_address', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_email"><?php esc_html_e('Email address', 'felan-framework') ?></label>
                <input class="point-mark" type="email" id="user_email" name="freelancer_email" placeholder="<?php esc_attr_e('Email', 'felan-framework') ?>" value="<?php echo esc_attr($freelancer_email); ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_phone_number', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_phone"><?php esc_html_e('Phone number', 'felan-framework') ?></label>
                <div class="tel-group">
                    <select name="prefix_code" class="felan-select2 prefix-code">
                        <?php
                        $prefix_code = phone_prefix_code();
                        foreach ($prefix_code as $key => $value) {
                            echo '<option value="' . $key . '" data-dial-code="' . $value['code'] . '" ' . ($phone_code == $key ? 'selected' : '') . '>' . $value['name'] . ' (' . $value['code'] . ')</option>';
                        }
                        ?>
                    </select>
                    <input class="point-mark" type="tel" id="author_mobile_number" name="freelancer_phone" value="<?php echo esc_attr($freelancer_phone); ?>" placeholder="<?php esc_attr_e('Phone', 'felan-framework'); ?>">
                </div>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_current_position', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_current_position"><?php esc_html_e('Current Position', 'felan-framework') ?></label>
                <input class="point-mark" type="text" id="freelancer_current_position" name="freelancer_current_position" value="<?php echo esc_attr($freelancer_current_position); ?>" placeholder="<?php esc_attr_e('Ex: UI/UX Designer', 'felan-framework'); ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_categories', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_categories"><?php esc_html_e('Categories', 'felan-framework') ?></label>
                <div class="select2-field">
                    <select class="point-mark felan-select2" name="freelancer_categories" id="freelancer_categories">
                        <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_categories', true); ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_description', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-12">
                <label for="freelancer_des"><?php esc_html_e('Description', 'felan-framework') ?></label>
                <?php
                $content = $freelancer_des;
                $editor_id = 'freelancer_des';
                $settings = array(
                    'wpautop' => true,
                    'media_buttons' => false,
                    'textarea_name' => $editor_id,
                    'textarea_rows' => get_option('default_post_edit_rows', 8),
                    'tabindex' => '',
                    'editor_css' => '',
                    'editor_class' => '',
                    'teeny' => false,
                    'dfw' => false,
                    'tinymce' => true,
                    'quicktags' => true
                );
                wp_editor(html_entity_decode(stripcslashes($content)), $editor_id, $settings);
                ?>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_date_of_birth', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_dob"><?php esc_html_e('Date of Birth', 'felan-framework') ?></label>
                <input class="point-mark datepicker" type="text" placeholder="<?php echo $date_format; ?>" id="freelancer_dob" name="freelancer_dob" value="<?php echo esc_attr($freelancer_dob); ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_age', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_age"><?php esc_html_e('Age', 'felan-framework') ?></label>
                <div class="select2-field">
                    <select class="point-mark felan-select2" name="freelancer_age" id="freelancer_age">
                        <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_ages', true); ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_gender', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_gender"><?php esc_html_e('Gender', 'felan-framework') ?></label>
                <div class="select2-field">
                    <select class="point-mark felan-select2" name="freelancer_gender" id="freelancer_gender">
                        <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_gender', true); ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!in_array('fields_closing_languages', $hide_freelancer_fields)) : ?>
            <?php if ($enable_freelancer_language_multiple === '1') : ?>
                <div class="form-group col-md-6">
                    <label for="freelancer_languages"><?php esc_html_e('Languages', 'felan-framework') ?></label>
                    <div class="form-select">
                        <div class="select2-field select2-multiple point-mark">
                            <select data-placeholder="<?php esc_attr_e('Select languages', 'felan-framework'); ?>" multiple="multiple" class="felan-select2" name="freelancer_languages">
                                <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_languages', false); ?>
                            </select>
                        </div>
                        <i class="far fa-angle-down"></i>
                    </div>
                </div>
            <?php else : ?>
                <div class="form-group col-md-6">
                    <label for="freelancer_languages"><?php esc_html_e('Languages', 'felan-framework') ?></label>
                    <div class="select2-field">
                        <select class="felan-select2 point-mark" name="freelancer_languages" id="freelancer_languages">
                            <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_languages', true); ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!in_array('fields_freelancer_qualification', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_qualification"><?php esc_html_e('Qualification', 'felan-framework') ?></label>
                <div class="select2-field">
                    <select class="point-mark felan-select2" name="freelancer_qualification" id="freelancer_qualification">
                        <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_qualification', true); ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_experience', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_yoe"><?php esc_html_e('Years of Experience', 'felan-framework') ?></label>
                <div class="select2-field">
                    <select class="point-mark felan-select2" name="freelancer_yoe" id="freelancer_yoe">
                        <?php
                        $args = array(
                            'orderby'    => 'name',
                            'parent'     => 0,
                            'hide_empty' => false,
                            'meta_key' => 'freelancer_experience_order',
                            'orderby' => 'meta_value_num',
                            'meta_type' => 'DATE',
                        );

                        $post_id = $freelancer_id;
                        $taxonomy_name = 'freelancer_yoe';
                        $terms = get_terms($taxonomy_name, $args);
                        $result = array();

                        foreach ($terms as $term) {
                            $term_children            = get_terms($taxonomy_name, array(
                                'parent'     => $term->term_id,
                                'hide_empty' => false
                            ));
                            $result[$term->term_id] = array();
                            foreach ($term_children as $child) {
                                $child_level_2                               = get_terms($taxonomy_name, array(
                                    'parent'     => $child->term_id,
                                    'hide_empty' => false
                                ));
                                $result[$term->term_id][$child->term_id] = array();
                                foreach ($child_level_2 as $grandchild) {
                                    $result[$term->term_id][$child->term_id][$grandchild->term_id] = array();
                                }
                            }
                        }

                        $target_by_id   = array();
                        $tax_terms      = get_the_terms($post_id, $taxonomy_name);
                        if (!empty($tax_terms)) {
                            foreach ($tax_terms as $tax_term) {
                                $target_by_id[] = $tax_term->term_id;
                            }
                        }
                        if ($target_by_id == 0 || empty($target_by_id)) {
                            echo '<option value="" selected>' . esc_html__('Select an option', 'felan-framework') . '</option>';
                        } else {
                            echo '<option value="">' . esc_html__('Select an option', 'felan-framework') . '</option>';
                        }
                        felan_get_taxonomy_target_by_id($result, $target_by_id, $taxonomy_name);
                        ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_salary', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="freelancer_offer_salary"><?php esc_html_e('Offer Salary', 'felan-framework') ?></label>
                <input class="point-mark" type="number" id="freelancer_offer_salary" name="freelancer_offer_salary" value="<?php echo esc_attr($freelancer_offer_salary); ?>" placeholder="<?php esc_html_e('Ex: 100', 'felan-framework') ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_salary', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label><?php esc_html_e('Salary type', 'felan-framework'); ?></label>
                <div class="select2-field">
                    <select name="freelancer_salary_type" class="felan-select2 point-mark">
                        <option <?php if ($freelancer_salary_type == '') {
                                    echo 'selected';
                                } ?> value=""><?php esc_html_e('None', 'felan-framework'); ?></option>
                        <option <?php if ($freelancer_salary_type == 'hr') {
                                    echo 'selected';
                                } ?> value="hr"><?php esc_html_e('Hourly', 'felan-framework'); ?></option>
                        <option <?php if ($freelancer_salary_type == 'day') {
                                    echo 'selected';
                                } ?> value="day"><?php esc_html_e('Daily', 'felan-framework'); ?></option>
                        <option <?php if ($freelancer_salary_type == 'month') {
                                    echo 'selected';
                                } ?> value="month"><?php esc_html_e('Monthly', 'felan-framework'); ?></option>
                        <option <?php if ($freelancer_salary_type == 'year') {
                                    echo 'selected';
                                } ?> value="year"><?php esc_html_e('Yearly', 'felan-framework'); ?></option>
                    </select>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_freelancer_salary', $hide_freelancer_fields)) : ?>
            <div class="form-group col-md-6">
                <label><?php esc_html_e('Currency', 'felan-framework'); ?></label>
                <div class="select2-field">
                    <select name="freelancer_currency_type" class="felan-select2">
                        <?php felan_get_select_currency_type(true); ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>