<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $project_data, $hide_project_fields, $current_user;
$image_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
$project_thumbnail_id = get_post_thumbnail_id($project_data->ID);
$project_thumbnail_id = !empty($project_thumbnail_id) ? $project_thumbnail_id : '';
$project_image_url = get_the_post_thumbnail_url($project_data->ID, 'full');
$project_image_url = !empty($project_image_url) ? $project_image_url : '';
felan_get_thumbnail_enqueue();
?>
<?php if (!in_array('fields_project_cover_image', $hide_project_fields)) : ?>
    <div class="project-fields-warpper">
        <div class="project-fields-thumbnail felan-fields-thumbnail">
            <label><?php esc_html_e('Cover image', 'felan-framework'); ?></label>
            <div class="form-field">
                <div id="felan_thumbnail_errors" class="errors-log"></div>
                <div id="felan_thumbnail_container" class="file-upload-block preview">
                    <div id="felan_thumbnail_view" data-image-id="<?php echo $project_thumbnail_id; ?>" data-image-url="<?php echo $project_image_url; ?>"></div>
                    <div id="felan_add_thumbnail">
                        <i class="far fa-arrow-from-bottom large"></i>
                        <p id="felan_drop_thumbnail">
                            <button type="button" id="felan_select_thumbnail"><?php esc_html_e('Click here', 'felan-framework') ?></button>
                            <?php esc_html_e(' or drop files to upload', 'felan-framework') ?>
                        </p>
                    </div>
                    <input type="hidden" class="thumbnail_url form-control" name="project_thumbnail_url" value="<?php echo $project_image_url; ?>" id="thumbnail_url">
                    <input type="hidden" class="thumbnail_id" name="project_thumbnail_id" value="<?php echo $project_thumbnail_id; ?>" id="thumbnail_id" />
                </div>
            </div>
            <p class="felan-thumbnail-size"><?php esc_html_e('The cover image size should be max 1920 x 400px', 'felan-framework') ?></p>
        </div>
    </div>
<?php endif; ?>