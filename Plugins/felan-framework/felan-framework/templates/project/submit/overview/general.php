<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_project_fields;
?>

<div class="row">
    <?php if (!in_array('fields_project_title', $hide_project_fields)) : ?>
        <div class="form-group col-md-12">
            <label for="project_title"><?php esc_html_e('Title', 'felan-framework') ?> <sup>*</sup></label>
            <input type="text" id="project_title" name="project_title" placeholder="<?php esc_attr_e('Enter title', 'felan-framework') ?>">
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_project_category', $hide_project_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Categories', 'felan-framework') ?> <sup>*</sup></label>
            <div class="select2-field select2-multiple">
                <select data-placeholder="<?php esc_attr_e('Select categories', 'felan-framework'); ?>" multiple="multiple" name="project_categories" class="felan-select2">
                    <?php felan_get_taxonomy('project-categories', false, false); ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_project_career', $hide_project_fields)) : ?>
        <div class="form-group col-md-6">
            <label><?php esc_html_e('Career Level', 'felan-framework') ?> <sup>*</sup></label>
            <div class="select2-field">
                <select name="project_career" class="felan-select2">
                    <?php felan_get_taxonomy('project-career', false, true); ?>
                </select>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_project_language', $hide_project_fields)) : ?>
        <div class="form-group col-md-12">
            <label><?php esc_html_e('Languages', 'felan-framework') ?> <sup>*</sup></label>
            <div class="form-select">
                <div class="select2-field select2-multiple">
                    <select data-placeholder="<?php esc_attr_e('Select languages', 'felan-framework'); ?>" multiple="multiple" class="felan-select2" name="project_language">
                        <?php felan_get_taxonomy('project-language', false, false); ?>
                    </select>
                </div>
                <i class="far fa-angle-down"></i>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_project_description', $hide_project_fields)) : ?>
        <div class="form-group col-md-12">
			<div class="flex">
            	<label class="label-des-project">
					<?php esc_html_e('Description', 'felan-framework'); ?>
					<sup>*</sup>
				</label>
				<?php
				$enable_ai_helper = felan_get_option('enable_ai_helper');
				$ai_key = felan_get_option('ai_key');
				if ($enable_ai_helper == 1 && $ai_key != '') {
				?>
					<div class="ai-helper-wrapper">
						<span class="ai-helper" data-popup="ai-popup"><i class="far fa-magic"></i><?php esc_html_e('AI Helper', 'felan-framework'); ?></span>
					</div>
				<?php
				}
				?>
			</div>

            <?php
            $content = '';
            $editor_id = 'project_des';
            $settings = array(
                'wpautop' => true,
                'media_buttons' => false,
                'textarea_name' => $editor_id,
                'textarea_rows' => get_option('default_post_edit_rows', 8),
                'tabindex' => '',
                'editor_css' => '',
                'editor_class' => '',
                'teeny' => false,
                'dfw' => false,
                'tinymce' => true,
                'quicktags' => true
            );
            wp_editor(html_entity_decode(stripcslashes($content)), $editor_id, $settings); ?>
        </div>
    <?php endif; ?>
</div>