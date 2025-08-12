<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_freelancer_fields, $current_user, $freelancer_data;
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
$image_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
felan_get_gallery_enqueue();
?>
<div class="felan-freelancer-galleries felan-upload-gallery block-from">
    <h6><?php esc_html_e('Gallery', 'felan-framework') ?></h6>
    <div class="freelancer-fields-media felan-fields-gallery">
        <label><?php esc_html_e('Image', 'felan-framework'); ?></label>
        <div class="freelancer-fields freelancer-fields-file freelancer-gallery-image">
            <div class="field-media-warpper">
                <div class="media-gallery">
                    <div id="felan_gallery_thumbs">
                        <?php
                        $freelancer_img_arg = get_post_meta($freelancer_data->ID, FELAN_METABOX_PREFIX . 'freelancer_galleries', false);
                        $freelancer_images = (isset($freelancer_img_arg) && is_array($freelancer_img_arg) && count($freelancer_img_arg) > 0) ? $freelancer_img_arg[0] : '';
                        $freelancer_images = explode('|', $freelancer_images);
                        $freelancer_images = array_unique($freelancer_images);
                        if (!empty($freelancer_images[0])) {
                            foreach ($freelancer_images as $attach_id) {
                                echo '<div class="media-thumb-wrap">';
                                echo '<figure class="media-thumb">';
                                echo wp_get_attachment_image($attach_id, 'thumbnail');
                                echo '<div class="media-item-actions">';
                                if ($user_demo == 'yes') { ?>
                                    <a class="btn-add-to-message" data-text="<?php echo esc_attr('Oops! Sorry. This action is restricted on the demo site.', 'felan-framework'); ?>" href="#">
                                        <i class="far fa-trash-alt large"></i>
                                    </a>
                        <?php } else {
                                    echo '<a class="icon icon-gallery-delete" data-attachment-id="' . intval($attach_id) . '" href="javascript:void(0)">';
                                    echo '<i class="far fa-trash-alt large"></i>';
                                    echo '</a>';
                                }
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
</div>