<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_project_fields, $current_user;
$image_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
felan_get_thumbnail_enqueue();
?>
<?php if (!in_array('fields_project_cover_image', $hide_project_fields)) : ?>
    <div class="project-fields-warpper">
        <div class="project-fields-thumbnail felan-fields-thumbnail">
            <label><?php esc_html_e('Cover image', 'felan-framework'); ?></label>
            <div class="form-field">
                <div id="felan_thumbnail_errors" class="errors-log"></div>
                <div id="felan_thumbnail_container" class="file-upload-block preview">
                    <div id="felan_thumbnail_view"></div>
                    <div id="felan_add_thumbnail">
                        <i class="far fa-arrow-from-bottom large"></i>
                        <p id="felan_drop_thumbnail">
                            <button type="button" id="felan_select_thumbnail"><?php esc_html_e('Click here', 'felan-framework') ?></button>
                            <?php esc_html_e(' or drop files to upload', 'felan-framework') ?>
                        </p>
                    </div>
                    <input type="hidden" class="thumbnail_url form-control" name="project_thumbnail_url" value="" id="thumbnail_url">
                    <input type="hidden" class="thumbnail_id" name="project_thumbnail_id" value="" id="thumbnail_id" />
                </div>
            </div>
            <p class="felan-thumbnail-size"><?php esc_html_e('The cover image size should be max 1920 x 400px', 'felan-framework') ?></p>
        </div>
    </div>
<?php endif; ?>