<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$cv_file = felan_get_option('felan-cv-type');
$cv_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
$text = '<i class="far fa-arrow-from-bottom large"></i> ' . esc_attr(sprintf(esc_html__('Upload CV (%s)', 'felan-framework'), $cv_file));
$upload_nonce = wp_create_nonce('felan_thumbnail_allow_upload');
$url = FELAN_AJAX_URL .  '?action=felan_thumbnail_upload_ajax&nonce=' . esc_attr($upload_nonce);

wp_enqueue_script('plupload');
wp_enqueue_script('jquery-validate');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'upload-cv');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'upload-cv',
    'felan_upload_cv_vars',
    array(
        'ajax_url'    => FELAN_AJAX_URL,
        'title'   => esc_html__('Valid file formats', 'felan-framework'),
        'cv_file' => $cv_file,
        'cv_max_file_size' => $cv_max_file_size,
        'upload_nonce' => $upload_nonce,
        'url' => $url,
        'text' => $text,
    )
);

global $current_user;
$user_id = $current_user->ID;
$freelancer_id =  $fileUrl = '';
if (in_array('felan_user_freelancer', (array)$current_user->roles)) {
    $args_freelancer = array(
        'post_type' => 'freelancer',
        'author' => $user_id,
    );
    $query = new WP_Query($args_freelancer);
    $freelancer_id = $query->post->ID;
}
$jobs_id = get_the_ID();
$jobs_select_apply = !empty(get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_apply')) ? get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_apply')[0] : '';

$freelancer_phone = $freelancer_email = '';
if (!empty($freelancer_id)) {
    $freelancer_resume = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_resume_id_list', false);
    $freelancer_resume = !empty($freelancer_resume) ? $freelancer_resume[0] : '';
    $fileName = basename(get_attached_file($freelancer_resume));
    $freelancer_email = !empty(get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_email')) ? get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_email')[0] : '';
    $freelancer_phone = !empty(get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_phone')) ? get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_phone')[0] : '';
    if (!empty(wp_get_attachment_url($freelancer_resume))) {
        $fileUrl = wp_get_attachment_url($freelancer_resume);
    }
}

$show_field_jobs_apply = felan_get_option('show_field_jobs_apply');
$freelancer_meta_data = get_post_custom($freelancer_id);
$freelancer_current_position = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_current_position']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_current_position'][0] : '';
$freelancer_categories = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_categories']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_categories'][0] : '';
$freelancer_dob = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_dob']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_dob'][0] : '';
$freelancer_age = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_age']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_age'][0] : '';
$freelancer_gender = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_gender']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_gender'][0] : '';
$freelancer_languages = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_languages']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_languages'][0] : '';
$freelancer_qualification = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_qualification']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_qualification'][0] : '';
$freelancer_yoe = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_yoe']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_yoe'][0] : '';
if (empty($show_field_jobs_apply)) {
    $col = 'col-md-12';
    $max_width = '100%';
} else {
    $col = 'col-md-6';
    $max_width = '550px';
}
?>
<form action="#" method="post" class="form-popup form-popup-apply" id="felan_form_apply_jobs" enctype="multipart/form-data">
    <div class="bg-overlay"></div>
    <div class="apply-popup custom-scrollbar" style="max-width: <?php echo $max_width; ?>">
        <a href="#" class="btn-close"><i class="far fa-times"></i></a>
        <h5><?php esc_html_e('Apply for this job', 'felan-framework') ?></h5>
        <div class="row">
            <div class="form-group <?php echo $col; ?>">
                <label for="apply_email"><?php esc_html_e('Email address', 'felan-framework') ?><sup> *</sup></label>
                <input type="email" id="apply_email" name="apply_emaill" placeholder="<?php esc_attr_e('Enter email', 'felan-framework') ?>" value="<?php echo esc_attr($freelancer_email) ?>">
            </div>
            <div class="form-group <?php echo $col; ?>">
                <?php
                $default_phone_number = felan_get_option('default_phone_number');
                ?>
                <label for="apply_phone"><?php esc_html_e('Phone', 'felan-framework') ?><sup> *</sup></label>
                <div class="tel-group">
                    <select name="prefix_code" class="felan-select2 prefix-code">
                        <?php
                        $prefix_code = phone_prefix_code();
                        foreach ($prefix_code as $key => $value) {
                            echo '<option value="' . $key . '" data-dial-code="' . $value['code'] . '" ' . ($default_phone_number == $key ? 'selected' : '') . '>' . $value['name'] . ' (' . $value['code'] . ')</option>';
                        }
                        ?>
                    </select>
                    <input type="tel" id="apply_phone" name="apply_phone" placeholder="<?php esc_attr_e('Enter phone', 'felan-framework') ?>" value="<?php echo esc_attr($freelancer_phone) ?>">
                </div>
            </div>

            <?php if (!empty($show_field_jobs_apply)) : ?>
                <?php if (in_array('position', $show_field_jobs_apply)) : ?>
                    <div class="form-group col-md-6">
                        <label for="freelancer_current_position"><?php esc_html_e('Current Position', 'felan-framework') ?></label>
                        <input class="point-mark" type="text" id="freelancer_current_position" name="freelancer_current_position" value="<?php echo esc_attr($freelancer_current_position); ?>" placeholder="<?php esc_attr_e('Ex: UI/UX Designer', 'felan-framework'); ?>">
                    </div>
                <?php endif; ?>
                <?php if (in_array('categories', $show_field_jobs_apply)) : ?>
                    <div class="form-group col-md-6">
                        <label for="freelancer_categories"><?php esc_html_e('Categories', 'felan-framework') ?></label>
                        <div class="select2-field">
                            <select class="point-mark felan-select2" name="freelancer_categories" id="freelancer_categories">
                                <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_categories', true); ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (in_array('date', $show_field_jobs_apply)) : ?>
                    <div class="form-group col-md-6">
                        <label for="freelancer_dob"><?php esc_html_e('Date of Birth', 'felan-framework') ?></label>
                        <input class="point-mark" type="date" id="freelancer_dob" name="freelancer_dob" value="<?php echo esc_attr($freelancer_dob); ?>">
                    </div>
                <?php endif; ?>
                <?php if (in_array('age', $show_field_jobs_apply)) : ?>
                    <div class="form-group col-md-6">
                        <label for="freelancer_age"><?php esc_html_e('Age', 'felan-framework') ?></label>
                        <div class="select2-field">
                            <select class="point-mark felan-select2" name="freelancer_age" id="freelancer_age">
                                <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_ages', true); ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (in_array('gender', $show_field_jobs_apply)) : ?>
                    <div class="form-group col-md-6">
                        <label for="freelancer_gender"><?php esc_html_e('Gender', 'felan-framework') ?></label>
                        <div class="select2-field">
                            <select class="point-mark felan-select2" name="freelancer_gender" id="freelancer_gender">
                                <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_gender', true); ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (in_array('languages', $show_field_jobs_apply)) : ?>
                    <div class="form-group col-md-6">
                        <label for="freelancer_languages"><?php esc_html_e('Languages', 'felan-framework') ?></label>
                        <div class="select2-field">
                            <select class="felan-select2 point-mark" name="freelancer_languages" id="freelancer_languages">
                                <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_languages', true); ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (in_array('qualification', $show_field_jobs_apply)) : ?>
                    <div class="form-group col-md-6">
                        <label for="freelancer_qualification"><?php esc_html_e('Qualification', 'felan-framework') ?></label>
                        <div class="select2-field">
                            <select class="point-mark felan-select2" name="freelancer_qualification" id="freelancer_qualification">
                                <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_qualification', true); ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (in_array('experience', $show_field_jobs_apply)) : ?>
                    <div class="form-group col-md-6">
                        <label for="freelancer_yoe"><?php esc_html_e('Years of Experience', 'felan-framework') ?></label>
                        <div class="select2-field">
                            <select class="point-mark felan-select2" name="freelancer_yoe" id="freelancer_yoe">
                                <?php
                                $args = array(
                                    'orderby'    => 'name',
                                    'parent'     => 0,
                                    'hide_empty' => false,
                                    'meta_key' => 'freelancer_experience_order',
                                    'orderby' => 'meta_value_num',
                                    'meta_type' => 'DATE',
                                );

                                $post_id = $freelancer_id;
                                $taxonomy_name = 'freelancer_yoe';
                                $terms = get_terms($taxonomy_name, $args);
                                $result = array();

                                foreach ($terms as $term) {
                                    $term_children            = get_terms($taxonomy_name, array(
                                        'parent'     => $term->term_id,
                                        'hide_empty' => false
                                    ));
                                    $result[$term->term_id] = array();
                                    foreach ($term_children as $child) {
                                        $child_level_2                               = get_terms($taxonomy_name, array(
                                            'parent'     => $child->term_id,
                                            'hide_empty' => false
                                        ));
                                        $result[$term->term_id][$child->term_id] = array();
                                        foreach ($child_level_2 as $grandchild) {
                                            $result[$term->term_id][$child->term_id][$grandchild->term_id] = array();
                                        }
                                    }
                                }

                                $target_by_id   = array();
                                $tax_terms      = get_the_terms($post_id, $taxonomy_name);
                                if (!empty($tax_terms)) {
                                    foreach ($tax_terms as $tax_term) {
                                        $target_by_id[] = $tax_term->term_id;
                                    }
                                }
                                if ($target_by_id == 0 || empty($target_by_id)) {
                                    echo '<option value="" selected>' . esc_html__('Select an option', 'felan-framework') . '</option>';
                                } else {
                                    echo '<option value="">' . esc_html__('Select an option', 'felan-framework') . '</option>';
                                }
                                felan_get_taxonomy_target_by_id($result, $target_by_id, $taxonomy_name);
                                ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="form-group col-md-12">
                <label for="apply_message"><?php esc_html_e('Message', 'felan-framework') ?><sup> *</sup></label>
                <textarea id="apply_message" name="apply_message" rows="4" cols="50"></textarea>
            </div>
            <div class="form-group col-md-12 felan-upload-cv">
                <div class="form-field">
                    <div id="cv_errors_log" class="errors-log"></div>
                    <div id="felan_cv_plupload_container" class="file-upload-block preview">
                        <div class="felan_cv_file felan_add-cv">
                            <p id="felan_drop_cv">
                                <?php if (!empty($fileName)) { ?>
                                    <button type="button" id="felan_select_cv">
                                        <i class="far fa-arrow-from-bottom large"></i>
                                        <?php esc_html_e($fileName); ?>
                                    </button>
                                <?php } else { ?>
                                    <button type="button" id="felan_select_cv">
                                        <i class="far fa-arrow-from-bottom large"></i>
                                        <?php echo esc_attr(sprintf(esc_html__('Upload CV (%s)', 'felan-framework'), $cv_file)); ?>
                                    </button>
                                <?php } ?>
                            </p>
                        </div>
                        <input type="hidden" class="cv_url form-control" name="jobs_cv_url" value="<?php echo esc_attr($fileUrl) ?>" id="cv_url">
                        <input type="hidden" class="type_apply form-control" name="type_apply" value="<?php esc_html_e($jobs_select_apply); ?>" id="type_apply">
                    </div>
                </div>
            </div>
        </div>
        <div class="message_error"></div>
        <div class="button-warpper">
            <a href="#" class="felan-button button-outline button-block button-cancel"><?php esc_html_e('Cancel', 'felan-framework'); ?></a>
            <button type="submit" class="felan-button button-block btn-submit-apply-jobs" id="btn-apply-jobs-<?php echo $jobs_id ?>" data-jobs_id="<?php echo $jobs_id ?>" data-freelancer_id="<?php echo $freelancer_id ?>">
                <?php esc_html_e('Apply Jobs', 'felan-framework'); ?>
                <span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
            </button>
        </div>
    </div>
</form>