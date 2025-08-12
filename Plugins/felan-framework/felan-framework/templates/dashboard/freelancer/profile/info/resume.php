<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_freelancer_fields, $freelancer_data, $freelancer_meta_data;
$freelancer_resume = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_resume_id_list']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_resume_id_list'][0] : '';
$filename = basename(get_attached_file($freelancer_resume));
$ajax_url = admin_url('admin-ajax.php');
$cv_file = felan_get_option('felan-cv-type');
$cv_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');

$upload_nonce = wp_create_nonce('felan_thumbnail_allow_upload');
$url = FELAN_AJAX_URL . '?action=felan_thumbnail_upload_ajax&nonce=' . esc_attr($upload_nonce);
$text = '<i class="far fa-arrow-from-bottom large"></i> ' . esc_html__('Browse', 'felan-framework');


wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'upload-cv');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'upload-cv',
    'felan_upload_cv_vars',
    array(
        'ajax_url' => $ajax_url,
        'title' => esc_html__('Valid file formats', 'felan-framework'),
        'cv_file' => $cv_file,
        'cv_max_file_size' => $cv_max_file_size,
        'upload_nonce' => $upload_nonce,
        'url' => $url,
        'text' => $text,
    )
);
$cv_file = felan_get_option('felan-cv-type');
?>
<?php if (!in_array('fields_freelancer_resume', $hide_freelancer_fields)) : ?>
    <div class="resume block-from">
        <h6><?php esc_html_e('Resume', 'felan-framework') ?></h6>
        <div class="freelancer-resume">
            <div class="form-group col-md-12 felan-upload-cv">
                <label><?php esc_html_e('CV Attachment', 'felan-framework'); ?></label>
                <div class="form-field">
                    <div id="cv_errors_log" class="errors-log"></div>
                    <div id="felan_cv_plupload_container" class="file-upload-block preview" style="display: inline-block;">
                        <div class="felan_cv_file felan_add-cv">
                            <p id="felan_drop_cv" data-attachment-id="<?php esc_attr_e($freelancer_resume) ?>">
                                <button class="felan-button" type="button" id="felan_select_cv">
                                    <i class="far fa-arrow-from-bottom large"></i>
                                    <?php if (!empty($freelancer_resume)) { ?>
                                        <span><?php esc_html_e($filename); ?></span>
                                    <?php } else { ?>
                                        <span><?php esc_html_e('Browse', 'felan-framework'); ?></span>
                                    <?php } ?>
                                </button>
                                <?php if (!empty($freelancer_resume)) { ?>
                                    <a class="icon cv-icon-delete" data-attachment-id="<?php esc_attr_e($freelancer_resume) ?>" href="#"><i class="far fa-trash-alt large"></i></a>
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <div class="file-type"><?php echo esc_attr(sprintf(esc_html__('Upload file: %s', 'felan-framework'), $cv_file)); ?></div>
                </div>
            </div>

        </div>
    </div>
<?php endif; ?>