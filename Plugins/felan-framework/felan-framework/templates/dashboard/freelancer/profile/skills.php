<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly 
}
$freelancer_id = felan_get_post_id_freelancer();
$freelancer_skills = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_skills', false);
$freelancer_skills = !empty($freelancer_skills) ?  $freelancer_skills[0] : '';
?>

<div id="tab-skills" class="tab-info">
    <div class="skills-info block-from">
        <h5><?php esc_html_e('Skills', 'felan-framework') ?></h5>
        <div class="sub-head"><?php esc_html_e('We recommend at least one skill entry', 'felan-framework') ?></div>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="freelancer_skills"><?php esc_html_e('Select Skills', 'felan-framework') ?></label>
                <div class="form-select">
                    <div class="select2-field select2-multiple point-mark">
                        <select data-placeholder="<?php esc_attr_e('Select skills', 'felan-framework'); ?>" multiple="multiple" class="felan-select2" name="freelancer_skills">
                            <?php felan_get_taxonomy_by_post_id($freelancer_id, 'freelancer_skills', false); ?>
                        </select>
                    </div>
                    <i class="far fa-angle-down"></i>
                </div>
            </div>
        </div>
    </div>
    <?php felan_custom_field_freelancer('skills'); ?>
</div>