<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$freelancer_id = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id;
}
$freelancer_skills = get_the_terms($freelancer_id, 'freelancer_skills');
if ($freelancer_skills == false || is_wp_error($freelancer_skills)) {
    return;
}
?>
<div class="block-archive-inner freelancer-single-field">
    <div class="freelancer-skills">
        <h3><?php echo esc_html__('Skills', 'felan-framework') ?></h3>
        <?php foreach ($freelancer_skills as $skill) {
            $skill_link = get_term_link($skill, 'freelancer_skills'); ?>
            <a href="<?php echo esc_url($skill_link); ?>" class="label label-skills">
                <?php esc_html_e($skill->name); ?>
            </a>
        <?php } ?>
    </div>
    <?php felan_custom_field_single_freelancer('skills'); ?>
</div>