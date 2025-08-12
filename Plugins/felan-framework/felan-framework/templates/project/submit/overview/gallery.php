<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_project_fields;
$image_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
felan_get_gallery_enqueue();
?>
<?php if (!in_array('fields_closing_gallery', $hide_project_fields)) : ?>
    <div class="project-fields-media felan-fields-gallery">
        <label><?php esc_html_e('Image', 'felan-framework'); ?></label>
        <div class="project-fields project-fields-file project-gallery-image">
            <div class="field-media-warpper">
                <div class="media-gallery">
                    <div id="felan_gallery_thumbs"></div>
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