<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_service_fields;
?>
<?php if (!in_array('fields_service_skills', $hide_service_fields)) : ?>
    <div class="form-group col-md-12">
        <div class="skills-info">
            <label for="service_skills"><?php esc_html_e('Select Skills', 'felan-framework') ?></label>
            <div class="form-select">
                <div class="select2-field select2-multiple">
                    <select data-placeholder="<?php esc_attr_e('Select skills', 'felan-framework'); ?>" multiple="multiple" class="felan-select2" name="service_skills">
                        <?php felan_get_taxonomy('service-skills', false, false); ?>
                    </select>
                </div>
                <i class="far fa-angle-down"></i>
            </div>
        </div>
    </div>
<?php endif; ?>