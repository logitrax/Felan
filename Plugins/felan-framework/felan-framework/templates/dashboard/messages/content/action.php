<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script('plupload');
wp_enqueue_script('jquery-validate');
$mess_image_upload_nonce = wp_create_nonce('felan_mess_image_allow_upload');
$mess_image_type = felan_get_option('felan_image_type');
$mess_image_id = rand(1, 999999);
$mess_image_file_size = felan_get_option('felan_image_max_file_size', '1000kb');

$file_type = felan_get_option('felan-cv-type');
$max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
$file_upload_nonce = wp_create_nonce('felan_thumbnail_allow_upload');
$file_url = FELAN_AJAX_URL . '?action=felan_thumbnail_upload_ajax&nonce=' . esc_attr($file_upload_nonce);


wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'mess_image');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'mess_image',
    'felan_mess_image_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'mess_image_title' => esc_html__('Valid file formats', 'felan-framework'),
        'mess_image_type' => $mess_image_type,
        'mess_image_file_size' => $mess_image_file_size,
        'mess_image_id' => $mess_image_id,
        'mess_image_upload_nonce' => $mess_image_upload_nonce,
    )
);

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'mess_file');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'mess_file',
    'felan_mess_file_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'title' => esc_html__('Valid file formats', 'felan-framework'),
        'file_type' => $file_type,
        'max_file_size' => $max_file_size,
        'file_upload_nonce' => $file_upload_nonce,
        'file_url' => $file_url,
    )
);
?>
<div class="content-write">
    <div id="felan_mess_image_view_<?php echo esc_attr($mess_image_id); ?>" class="custom-image-view"></div>
    <div id="felan_mess_file_view"></div>
    <textarea placeholder="<?php esc_attr_e('Write your message', 'felan-framework'); ?>" name="ricetheme_send_mess"></textarea>
</div>
<div class="mess-action">
    <div class="felan-fields-mess_image" data-mess-image-id="<?php echo esc_attr($mess_image_id); ?>">
        <div id="felan_mess_image_container_<?php echo esc_attr($mess_image_id); ?>" class="file-upload-block preview">
            <div id="felan_add_mess_image_<?php echo esc_attr($mess_image_id); ?>" class="custom-image-add">
                <p id="felan_drop_mess_image_<?php echo esc_attr($mess_image_id); ?>" style="margin-bottom: 0">
                    <button type="button" class="tooltip" id="felan_select_mess_image_<?php echo esc_attr($mess_image_id); ?>"
                            data-title="<?php echo esc_attr('Upload Image', 'felan-framework') ?>">
                        <i class="far fa-images"></i>
                    </button>
                </p>
            </div>
            <input type="hidden" class="mess_image_url" value="" id="mess_image_url_<?php echo esc_attr($mess_image_id); ?>">
            <input type="hidden" class="mess_image_id" value="" id="mess_image_id_<?php echo esc_attr($mess_image_id); ?>" />
        </div>
        <input type="hidden" class="image-id" value="<?php echo esc_attr($mess_image_id); ?>">
    </div>
    <div class="felan-upload-file">
        <div class="form-field">
            <div id="felan_file_container" class="file-upload-block preview">
                <div class="felan_cv_file felan_add-cv">
                    <p id="felan_drop_file" style="margin-bottom: 0">
                        <button type="button" class="tooltip" id="felan_select_file"
                                data-title="<?php echo esc_attr('Upload File', 'felan-framework') ?>">
                            <i class="far fa-file-upload"></i>
                        </button>
                    </p>
                </div>
                <input type="hidden" class="file_url form-control" name="file_url" value="" id="file_url">
            </div>
        </div>
    </div>
    <button id="btn-write-message">
        <?php esc_html_e('Send', 'felan-framework'); ?>
        <span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
    </button>
</div>
