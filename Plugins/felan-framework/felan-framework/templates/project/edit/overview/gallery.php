<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $project_data, $project_meta_data, $hide_project_fields;
$image_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
felan_get_gallery_enqueue();
?>
<?php if (!in_array('fields_closing_gallery', $hide_project_fields)) : ?>
    <div class="project-fields-media felan-fields-gallery">
        <label><?php esc_html_e('Image', 'felan-framework'); ?></label>
        <div class="project-fields project-fields-file project-gallery-image">
            <div class="field-media-warpper">
                <div class="media-gallery">
                    <div id="felan_gallery_thumbs">
                        <?php
                        $project_img_arg = get_post_meta($project_data->ID, FELAN_METABOX_PREFIX . 'project_images', false);
                        $project_images  = (isset($project_img_arg) && is_array($project_img_arg) && count($project_img_arg) > 0) ? $project_img_arg[0] : '';
                        $project_images  = explode('|', $project_images);
                        $project_images  = array_unique($project_images);
                        if (!empty($project_images[0])) {
                            foreach ($project_images as $attach_id) {
                                echo '<div class="media-thumb-wrap">';
                                echo '<figure class="media-thumb">';
                                echo wp_get_attachment_image($attach_id, 'thumbnail');
                                echo '<div class="media-item-actions">';
                                echo '<a class="icon icon-gallery-delete" data-attachment-id="' . intval($attach_id) . '" href="javascript:void(0)">';
                                echo '<i class="far fa-trash-alt large"></i>';
                                echo '</a>';
                                echo '</a>';
                                echo '<input type="hidden" class="felan_gallery_ids" name="felan_gallery_ids[]" value="' . intval($attach_id) . '">';
                                echo '<span style="display: none;" class="icon icon-loader">';
                                echo '<i class="far fa-spinner fa-spin large"></i>';
                                echo '</span>';
                                echo '</div>';
                                echo '</figure>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div id="felan_gallery_errors" class="errors-log"></div>
                <div class="felan-gallery-warpper">
                    <div class="felan-gallery-inner">
                        <div id="felan_gallery_container">
                            <button type="button" id="felan_select_gallery" class="btn btn-primary">
                                <i class="far fa-arrow-from-bottom large"></i>
                                <?php esc_html_e('Upload ', 'felan-framework'); ?>
                            </button>
                        </div>
                    </div>
                    <div class="field-note"><?php echo sprintf(__('Maximum file size: %s.', 'felan-framework'), $image_max_file_size); ?></div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>