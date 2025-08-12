<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $jobs_data, $jobs_meta_data, $hide_jobs_fields;
?>
<div class="jobs-fields-wrap">
    <div class="jobs-fields jobs-additional row">
        <?php
        $custom_field_jobs = felan_render_custom_field('jobs');
        if (count($custom_field_jobs) > 0) {
            foreach ($custom_field_jobs as $key => $field) {
                switch ($field['type']) {
                    case 'text':
        ?>
                        <div class="form-group col-lg-6">
                            <label><?php esc_html_e($field['title']); ?></label>
                            <input type="text" id="<?php echo esc_attr($field['id']); ?>" class="form-control" name="<?php echo esc_attr($field['id']); ?>" value="<?php if (isset($jobs_meta_data[$field['id']])) {
                                                                                                                                                                        echo sanitize_text_field($jobs_meta_data[$field['id']][0]);
                                                                                                                                                                    } ?>" placeholder="<?php esc_attr_e('Your Value', 'felan-framework'); ?>">
                        </div>
                    <?php
                        break;
                    case 'url':
                    ?>
                        <div class="form-group col-lg-6">
                            <label><?php esc_html_e($field['title']); ?></label>
                            <input type="url" id="<?php echo esc_attr($field['id']); ?>" class="form-control" name="<?php echo esc_attr($field['id']); ?>" value="<?php if (isset($jobs_meta_data[$field['id']])) {
                                                                                                                                                                        echo sanitize_text_field($jobs_meta_data[$field['id']][0]);
                                                                                                                                                                    } ?>" placeholder="<?php esc_attr_e('Your Url', 'felan-framework'); ?>">
                        </div>
                    <?php
                        break;
                    case 'textarea':
                    ?>
                        <div class="form-group col-12">
                            <label><?php esc_html_e($field['title']); ?></label>
                            <textarea name="<?php echo esc_attr($field['id']); ?>" rows="6" id="<?php echo esc_attr($field['id']); ?>" class="form-control"><?php if (isset($jobs_meta_data[$field['id']])) {
                                                                                                                                                                echo sanitize_text_field($jobs_meta_data[$field['id']][0]);
                                                                                                                                                            } ?></textarea>
                        </div>
                    <?php
                        break;
                    case 'select':
                    ?>
                        <div class="form-group col-lg-6">
                            <label class="d-block"><?php esc_html_e($field['title']); ?></label>
                            <div class="select2-field">
                                <select name="<?php echo esc_attr($field['id']); ?>" id="<?php echo esc_attr($field['id']); ?>" class="form-control felan-select2">
                                    <?php
                                    foreach ($field['options'] as $opt_value) : ?>
                                        <option value="<?php echo esc_attr($opt_value); ?>" <?php if (isset($jobs_meta_data[$field['id']]) && $jobs_meta_data[$field['id']][0] == $opt_value) {
                                                                                                echo 'selected';
                                                                                            } ?>><?php esc_html_e($opt_value); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php
                        break;
                    case 'checkbox_list':
                    ?>
                        <div class="form-group col-lg-6">
                            <label><?php esc_html_e($field['title']); ?></label>
                            <div class="felan-field-<?php echo esc_attr($field['id']); ?>">
                                <?php
                                $jobs_field = get_post_meta($jobs_data->ID, $field['id'], true);
                                if (empty($jobs_field)) {
                                    $jobs_field = array();
                                }
                                foreach ($field['options'] as $opt_value) :
                                    if (in_array($opt_value, $jobs_field)) : ?>
                                        <div class="checkbox-inline inline"><input class="custom-checkbox" type="checkbox" name="<?php echo esc_attr($field['id']); ?>[]" value="<?php echo esc_attr($opt_value); ?>" checked><?php esc_html_e($opt_value); ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="checkbox-inline inline"><input class="custom-checkbox" type="checkbox" name="<?php echo esc_attr($field['id']); ?>[]" value="<?php echo esc_attr($opt_value); ?>"><?php esc_html_e($opt_value); ?>
                                        </div>
                                <?php endif;
                                endforeach; ?>
                            </div>
                        </div>
                    <?php
                        break;

                    case 'image':
                        felan_get_custom_image_enqueue();
                        $jobs_field = get_post_meta($jobs_data->ID, $field['id'], true);
                        $custom_image_id = !empty($jobs_field['id']) ? $jobs_field['id'] : '';
                        $custom_image_url = !empty($jobs_field['url']) ? $jobs_field['url'] : '';
                    ?>
                        <div class="form-group col-12 felan-fields-custom_image">
                            <label><?php esc_html_e($field['title']); ?></label>
                            <div class="felan-field-<?php echo esc_attr($field['id']); ?>">
                                <div id="felan_custom_image_errors_<?php echo esc_attr($field['id']); ?>" class="errors-log"></div>
                                <div id="felan_custom_image_container_<?php echo esc_attr($field['id']); ?>" class="file-upload-block preview">
                                    <div id="felan_custom_image_view_<?php echo esc_attr($field['id']); ?>" class="custom-image-view"></div>
                                    <div id="felan_add_custom_image_<?php echo esc_attr($field['id']); ?>" class="custom-image-add">
                                        <i class="far fa-arrow-from-bottom large"></i>
                                        <p id="felan_drop_custom_image_<?php echo esc_attr($field['id']); ?>">
                                            <button type="button" id="felan_select_custom_image_<?php echo esc_attr($field['id']); ?>" class="select-custom-image"><?php esc_html_e('Click here', 'felan-framework') ?></button>
                                            <?php esc_html_e(' or drop files to upload', 'felan-framework') ?>
                                        </p>
                                    </div>
                                    <input type="hidden" class="custom_image_url" value="<?php echo $custom_image_url; ?>" id="custom_image_url_<?php echo esc_attr($field['id']); ?>">
                                    <input type="hidden" class="custom_image_id" value="<?php echo $custom_image_id; ?>" id="custom_image_id_<?php echo esc_attr($field['id']); ?>" />
                                </div>
                            </div>
                            <p class="felan-custom_image-size"><?php esc_html_e('The cover image size should be max 1920 x 400px', 'felan-framework') ?></p>
                            <input type="hidden" class="image-id" value="<?php echo esc_attr($field['id']); ?>">
                        </div>
                    <?php
                        break;
                    case 'file':
                        felan_get_custom_upload_enqueue();
                        $cv_file = felan_get_option('felan-cv-type');
                        $jobs_field = get_post_meta($jobs_data->ID, $field['id'], true);
                        $file_title = '';
                        if(!empty($jobs_field)){
                            $file_title = get_the_title(intval($jobs_field));
                        }
                        ?>
                        <div class="form-group col-12 felan-fields-custom_upload">
                            <label><?php esc_html_e($field['title']); ?></label>
                            <div class="felan-field-<?php echo esc_attr($field['id']); ?>">
                                <div id="cv_errors_log" class="errors-log"></div>
                                <div id="felan_cv_plupload_container" class="file-upload-block preview" style="display: inline-block;">
                                    <div class="felan_cv_file felan_add-cv">
                                        <p class="drop-cv-<?php echo esc_attr($field['id']); ?>" id="felan_drop_cv" data-attachment-id="<?php echo esc_attr($jobs_field);?>">
                                            <button class="felan-button" type="button" id="felan_select_cv">
                                                <i class="far fa-arrow-from-bottom large"></i>
                                                <span>
                                                    <?php if(!empty($file_title)) : ?>
                                                        <?php echo esc_html($file_title); ?>
                                                    <?php else: ?>
                                                        <?php esc_html_e('Browse', 'felan-framework'); ?>
                                                    <?php endif; ?>
                                                </span>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="file-type"><?php echo esc_attr(sprintf(esc_html__('Upload file: %s', 'felan-framework'), $cv_file)); ?></div>
                        </div>
                        <?php
                    break;
                }
            }
        }
        ?>
    </div>
</div>