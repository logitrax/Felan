<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);
$image_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
felan_get_avatar_enqueue();
?>
<div class="block-from add-new-company" id="project-submit-company">
    <h6><?php echo esc_html('Company', 'felan-framework') ?></h6>
    <div class="row add-new-company">
        <div class="form-group col-md-12 select-all-company">
            <div class="select-company">
                <label><?php esc_html_e('Select company', 'felan-framework') ?></label>
                <div class="select2-field">
                    <select name="project_select_company" class="felan-select2">
                        <?php felan_select_project_company(true); ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group col-md-12 new-company-form">
            <div class="company-fields-avatar felan-fields-avatar">
                <label style="margin-bottom: 5px;"><?php esc_html_e('Logo', 'felan-framework'); ?></label>
                <div class="form-field">
                    <div id="felan_avatar_errors" class="errors-log"></div>
                    <div id="felan_avatar_container" class="file-upload-block preview">
                        <div id="felan_avatar_view"></div>
                        <div id="felan_add_avatar">
                            <i class="far fa-arrow-from-bottom large"></i>
                            <p id="felan_drop_avatar">
                                <button type="button" id="felan_select_avatar"><?php esc_html_e('Upload', 'felan-framework') ?></button>
                            </p>
                        </div>
                        <input type="hidden" class="avatar_url form-control" name="company_avatar_url" value="" id="avatar_url">
                        <input type="hidden" class="avatar_id" name="company_avatar_id" value="" id="avatar_id" />
                    </div>
                </div>
                <div class="field-note"><?php echo sprintf(__('Maximum file size: %s.', 'felan-framework'), $image_max_file_size); ?></div>
            </div>
            <div class="info-company">
                <div class="company-title">
                    <label for="company_title"><?php esc_html_e('Company name', 'felan-framework') ?></label>
                    <input type="text" id="company_title" name="company_title" placeholder="<?php esc_attr_e('Name', 'felan-framework') ?>">
                </div>
                <div class="company-email">
                    <label><?php esc_html_e('Email', 'felan-framework') ?></label>
                    <input type="email" id="company_email" name="company_email" placeholder="<?php esc_attr_e('hello@domain.com', 'felan-framework') ?>">
                </div>
            </div>
        </div>
    </div>
</div>