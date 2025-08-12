<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_company_fields;
$default_phone_number = felan_get_option('default_phone_number');
$enable_add_new_company_categories = felan_get_option('enable_add_new_company_categories');
?>

<div class="row">
    <?php if (!in_array('fields_company_name', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label for="company_title"><?php esc_html_e('Company name', 'felan-framework') ?> <sup>*</sup></label>
            <input type="text" id="company_title" name="company_title" placeholder="<?php esc_attr_e('Name', 'felan-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_category', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Categories', 'felan-framework') ?> <sup>*</sup></label>
            <div class="select2-field">
                <select name="company_categories" class="felan-select2">
                    <?php felan_get_taxonomy('company-categories', false, true); ?>
                </select>
            </div>
        </div>
        <?php if ($enable_add_new_company_categories) : ?>
            <div class="form-group col-md-12">
                <label for="company_new_categories"><?php esc_html_e('Add New Categories', 'felan-framework'); ?></label>
                <input type="text" id="company_new_categories" name="company_new_categories" value="" placeholder="<?php esc_attr_e('Enter new Categories', 'felan-framework'); ?>">
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (!in_array('fields_company_url', $hide_company_fields)) : ?>
        <div class="form-group col-md-12">
            <label><?php esc_html_e('Company Url Slug', 'felan-framework') ?></label>
            <div class="company-url-warp">
                <input class="input-url" type="text" placeholder="<?php echo esc_url(get_post_type_archive_link('company')) ?>" disabled>
                <input class="input-slug" type="text" id="company_url" name="company_url" placeholder="<?php esc_attr_e('company-name', 'felan-framework') ?>">
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_about', $hide_company_fields)) : ?>
        <div class="form-group col-md-12">
            <label class="label-des-company"><?php esc_html_e('About company', 'felan-framework'); ?>
                <sup>*</sup></label>
            <?php
            $content = '';
            $editor_id = 'company_des';
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
    <?php if (!in_array('fields_company_website', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e(' Website', 'felan-framework'); ?></label>
            <input type="url" id="company_website" name="company_website" placeholder="<?php esc_attr_e('www.domain.com', 'felan-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_phone', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Phone Number', 'felan-framework'); ?></label>
            <div class="tel-group">
                <select name="prefix_code" class="felan-select2 prefix-code">
                    <?php
                    $prefix_code = phone_prefix_code();
                    foreach ($prefix_code as $key => $value) {
                        echo '<option value="' . $key . '" data-dial-code="' . $value['code'] . '" ' . ($default_phone_number == $key ? 'selected' : '') . '>' . $value['name'] . ' (' . $value['code'] . ')</option>';
                    }
                    ?>
                </select>
                <input type="tel" id="company_phone" name="company_phone" placeholder="<?php esc_attr_e('+00 12 334 5678', 'felan-framework') ?>">
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_email', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Email', 'felan-framework') ?> <sup>*</sup></label>
            <input type="email" id="company_email" name="company_email" placeholder="<?php esc_attr_e('hello@domain.com', 'felan-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_founded', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Founded in', 'felan-framework') ?></label>
            <div class="select2-field">
                <select name="company_founded" class="felan-select2">
                    <?php echo felan_get_company_founded(); ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_company_size', $hide_company_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Company size', 'felan-framework') ?> <sup>*</sup></label>
            <div class="select2-field">
                <select name="company_size" class="felan-select2">
                    <?php felan_get_taxonomy('company-size', false, true, false, true, 'company_size_order'); ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
</div>