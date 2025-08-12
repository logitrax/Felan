<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $project_data, $hide_project_fields;
?>
<?php if (!in_array('fields_project_skills', $hide_project_fields)) : ?>
    <div class="form-group col-md-12">
        <div class="skills-info">
            <label for="project_skills"><?php esc_html_e('Select Skills', 'felan-framework') ?></label>
            <div class="form-select">
                <div class="select2-field select2-multiple">
                    <select data-placeholder="<?php esc_attr_e('Select skills', 'felan-framework'); ?>" multiple="multiple" class="felan-select2" name="project_skills">
                        <?php felan_get_taxonomy_by_post_id($project_data->ID, 'project-skills', true); ?>
                    </select>
                </div>
                <i class="far fa-angle-down"></i>
            </div>
        </div>
    </div>
<?php endif; ?>