<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $company_data, $company_meta_data, $hide_company_fields;
$company_logo_arg = get_post_meta($company_data->ID, FELAN_METABOX_PREFIX . 'company_logo', false);
$company_logo_id = isset($company_logo_arg[0]['id']) ? $company_logo_arg[0]['id'] : '';
$company_logo_url = isset($company_logo_arg[0]['url']) ? $company_logo_arg[0]['url'] : '';
$company_thumbnail_id = get_post_thumbnail_id($company_data->ID);
$company_thumbnail_id = !empty($company_thumbnail_id) ? $company_thumbnail_id : '';
$company_image_url = get_the_post_thumbnail_url($company_data->ID, 'full');
$company_image_url = !empty($company_image_url) ? $company_image_url : '';
$image_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
felan_get_thumbnail_enqueue();
felan_get_avatar_enqueue();
?>
<div class="company-fields-warpper">
    <div class="company-fields-avatar felan-fields-avatar">
        <label><?php esc_html_e('Logo', 'felan-framework'); ?></label>
        <div class="form-field">
            <div id="felan_avatar_errors" class="errors-log"></div>
            <div id="felan_avatar_container" class="file-upload-block preview">
                <div id="felan_avatar_view" data-image-id="<?php echo $company_logo_id; ?>" data-image-url="<?php echo $company_logo_url; ?>"></div>
                <div id="felan_add_avatar">
                    <i class="far fa-arrow-from-bottom large"></i>
                    <p id="felan_drop_avatar">
                        <button type="button" id="felan_select_avatar"><?php esc_html_e('Upload', 'felan-framework') ?></button>
                    </p>
                </div>
                <input type="hidden" class="avatar_url form-control" name="company_avatar_url" value="<?php echo $company_logo_url; ?>" id="avatar_url">
                <input type="hidden" class="avatar_id" name="company_avatar_id" value="<?php echo $company_logo_id; ?>" id="avatar_id" />
            </div>
        </div>
        <div class="field-note"><?php echo sprintf(__('Maximum file size: %s.', 'felan-framework'), $image_max_file_size); ?></div>
    </div>
    <div class="company-fields-thumbnail felan-fields-thumbnail">
        <label><?php esc_html_e('Cover image', 'felan-framework'); ?></label>
        <div class="form-field">
            <div id="felan_thumbnail_errors" class="errors-log"></div>
            <div id="felan_thumbnail_container" class="file-upload-block preview">
                <div id="felan_thumbnail_view" data-image-id="<?php echo $company_thumbnail_id; ?>" data-image-url="<?php echo $company_image_url; ?>"></div>
                <div id="felan_add_thumbnail">
                    <i class="far fa-arrow-from-bottom large"></i>
                    <p id="felan_drop_thumbnail">
                        <button type="button" id="felan_select_thumbnail"><?php esc_html_e('Click here', 'felan-framework') ?></button>
                        <?php esc_html_e(' or drop files to upload', 'felan-framework') ?>
                    </p>
                </div>
                <input type="hidden" class="thumbnail_url form-control" name="company_thumbnail_url" value="<?php echo $company_thumbnail_id; ?>" id="thumbnail_url">
                <input type="hidden" class="thumbnail_id" name="company_thumbnail_id" value="<?php echo $company_image_url; ?>" id="thumbnail_id" />
            </div>
        </div>
        <p class="felan-thumbnail-size"><?php esc_html_e('The cover image size should be max 1920 x 400px', 'felan-framework') ?></p>
    </div>
</div>