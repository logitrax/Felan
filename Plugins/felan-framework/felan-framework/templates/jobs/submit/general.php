<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_jobs_fields, $current_user;
$jobs_id = get_the_ID();
$jobs_days_closing = felan_get_option('jobs_number_days', true);
$user_id = $current_user->ID;
$jobs_user_post_title = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'jobs_user_post_title', true);
$jobs_user_post_des = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'jobs_user_post_des', true);
$jobs_user_quantity = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'jobs_user_quantity', true);
$jobs_user_gender = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'jobs_user_gender', true);
$jobs_user_days_closing = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'jobs_user_days_closing', true);
$enable_add_new_job_categories = felan_get_option('enable_add_new_job_categories');
$enable_ai_helper = felan_get_option('enable_ai_helper');
$ai_key = felan_get_option('ai_key');
?>
<div class="row">
    <?php if (!in_array('fields_jobs_name', $hide_jobs_fields)) : ?>
        <div class="form-group col-md-12">
            <label for="jobs_title"><?php esc_html_e('Job title', 'felan-framework') ?> <sup>*</sup></label>
            <input type="text" id="jobs_title" name="jobs_title" placeholder="<?php esc_attr_e('Name', 'felan-framework') ?>" value="<?php echo $jobs_user_post_title ?>">
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_category', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Jobs Categories', 'felan-framework') ?> <sup>*</sup></label>
            <div class="select2-field select2-multiple">
                <select name="jobs_categories" data-placeholder="<?php esc_attr_e('Select categories', 'felan-framework'); ?>" multiple="multiple" class="felan-select2">
                    <?php felan_get_taxonomy('jobs-categories', false, false); ?>
                </select>
            </div>
        </div>
        <?php if ($enable_add_new_job_categories) : ?>
            <div class="form-group col-md-6">
                <label for="jobs_new_categories"><?php esc_html_e('Add New Categories', 'felan-framework'); ?></label>
                <input type="text" id="jobs_new_categories" name="jobs_new_categories" value="" placeholder="<?php esc_attr_e('Enter new Categories', 'felan-framework'); ?>">
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_type', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Job type', 'felan-framework') ?> <sup>*</sup></label>
            <div class="form-select">
                <div class="select2-field select2-multiple">
                    <select data-placeholder="<?php esc_attr_e('Select an option', 'felan-framework'); ?>" multiple="multiple" class="felan-select2" name="jobs_type">
                        <?php felan_get_taxonomy('jobs-type', false, false); ?>
                    </select>
                </div>
                <i class="far fa-angle-down"></i>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_skills', $hide_jobs_fields)) : ?>
        <div class="form-group col-md-12">
            <label><?php esc_html_e('Skills', 'felan-framework') ?> <sup>*</sup></label>
            <div class="form-select">
                <div class="select2-field select2-multiple">
                    <select data-placeholder="<?php esc_attr_e('Select skills', 'felan-framework'); ?>" multiple="multiple" class="felan-select2" name="jobs_skills">
                        <?php felan_get_taxonomy('jobs-skills', false, true); ?>
                    </select>
                </div>
                <i class="far fa-angle-down"></i>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_des', $hide_jobs_fields)) : ?>
        <div class="form-group col-md-12">
            <div class="flex">
                <label class="label-des-jobs"><?php esc_html_e('Description', 'felan-framework'); ?> <sup>*</sup></label>
                <?php
                if ($enable_ai_helper == 1 && $ai_key != '') {
                ?>
                    <div class="ai-helper-wrapper">
                        <span class="ai-helper" data-popup="ai-popup"><i class="far fa-magic"></i><?php esc_html_e('AI Helper', 'felan-framework'); ?></span>
                    </div>
                <?php
                }
                ?>
            </div>
            <?php
            if ($jobs_user_post_des) {
                $content = "$jobs_user_post_des";
            } else {
                $content = '';
            }
            $editor_id = 'jobs_des';
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
            wp_editor(html_entity_decode(stripcslashes($content)), $editor_id, $settings); ?>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_career', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Career level', 'felan-framework') ?></label>
            <div class="select2-field">
                <select name="jobs_career" class="felan-select2">
                    <?php felan_get_taxonomy('jobs-career', false, true); ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_experience', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Experience', 'felan-framework') ?></label>
            <div class="select2-field">
                <select name="jobs_experience" class="felan-select2">
                    <?php felan_get_taxonomy('jobs-experience', false, true, false, true, 'jobs_experience_order'); ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_qualification', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Qualification', 'felan-framework') ?></label>
            <div class="form-select">
                <div class="select2-field select2-multiple">
                    <select data-placeholder="<?php esc_attr_e('Select an option', 'felan-framework'); ?>" multiple="multiple" class="felan-select2" name="jobs_qualification">
                        <?php felan_get_taxonomy('jobs-qualification', false, true); ?>
                    </select>
                </div>
                <i class="far fa-angle-down"></i>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_quantity', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Quantity to be recruited', 'felan-framework') ?></label>
            <div class="select2-field">
                <select name="jobs_quantity" class="felan-select2">
                    <?php for ($quantity = 0; $quantity <= 10; $quantity++) {
                        if ($quantity == 0) { ?>
                            <option selected value=""><?php esc_attr_e('Select an option', 'felan-framework'); ?></option>
                        <?php
                        } else {
                        ?>
                            <option <?php if ($jobs_user_quantity == $quantity) {
                                        echo 'selected';
                                    } ?> value="<?php echo $quantity; ?>">
                                <?php if ($quantity == 10) {
                                    echo $quantity . '+';
                                } else {
                                    echo $quantity;
                                } ?>
                            </option>
                    <?php }
                    } ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_gender', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Gender', 'felan-framework') ?></label>
            <div class="select2-field">
                <select name="jobs_gender" class="felan-select2">
                    <?php felan_get_taxonomy('jobs-gender', false, true); ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_closing_days', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label for="jobs_days_closing"><?php esc_html_e('Closing days', 'felan-framework'); ?></label>
            <input type="text" id="jobs_days_closing" name="jobs_days_closing" placeholder="<?php echo $jobs_days_closing; ?>" value="<?php echo $jobs_user_days_closing; ?>">
        </div>
    <?php endif; ?>
</div>