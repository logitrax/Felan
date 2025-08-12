<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_jobs_fields, $current_user;
$user_id = $current_user->ID;
$jobs_user_thumbnail_id = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'jobs_user_thumbnail_ids', true);
$jobs_user_images = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'jobs_user_images', true);
$jobs_user_image_url = wp_get_attachment_image_src($jobs_user_thumbnail_id, 'full');
felan_get_thumbnail_enqueue();
?>
<div class="jobs-fields-thumbnail felan-fields-thumbnail">
    <label><?php esc_html_e('Cover image', 'felan-framework'); ?></label>
    <div class="form-field">
        <div id="felan_thumbnail_errors" class="errors-log"></div>
        <div id="felan_thumbnail_container" class="file-upload-block preview">
            <div id="felan_thumbnail_view" data-image-id="<?php echo $jobs_user_thumbnail_id; ?>" data-image-url="<?php if (!empty($jobs_user_image_url)) {
                                                                                                                        echo $jobs_user_image_url[0];
                                                                                                                    } ?>"></div>
            <div id="felan_add_thumbnail">
                <i class="far fa-arrow-from-bottom large"></i>
                <p id="felan_drop_thumbnail">
                    <button type="button" id="felan_select_thumbnail"><?php esc_html_e('Click here', 'felan-framework') ?></button>
                    <?php esc_html_e(' or drop files to upload', 'felan-framework') ?>
                </p>
            </div>
            <input type="hidden" class="thumbnail_url form-control" name="jobs_thumbnail_url" value="" id="thumbnail_url">
            <input type="hidden" class="thumbnail_id" name="jobs_thumbnail_id" value="" id="thumbnail_id" />
        </div>
    </div>
</div>
<p class="felan-thumbnail-size"><?php esc_html_e('The cover image size should be max 1920 x 400px', 'felan-framework') ?></p>