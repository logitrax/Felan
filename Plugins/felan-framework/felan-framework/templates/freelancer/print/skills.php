<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$freelancer_details_prints = felan_get_option('freelancer_details_prints');
foreach ($freelancer_details_prints as $print) {
    if (!in_array('enable_print_sp_skills', $freelancer_details_prints)) {
        return;
    }
}

$freelancer_skills = get_the_terms($freelancer_id, 'freelancer_skills');

if ($freelancer_skills == false || is_wp_error($freelancer_skills)) {
    return;
}

?>

<div class="block-archive-inner freelancer-single-field">
    <h4 class="title-freelancer"><?php esc_html_e('Skills', 'felan-framework') ?></h4>
    <div class="freelancer-skills">
        <?php foreach ($freelancer_skills as $skill) {
            $skill_link = get_term_link($skill, 'freelancer_skills'); ?>
            <a href="<?php echo esc_url($skill_link); ?>" class="label label-skills">
                <?php esc_html_e($skill->name); ?>
            </a>
        <?php } ?>
    </div>
    <?php
    $custom_field_freelancer = felan_render_custom_field('freelancer');
    $freelancer_meta_data = get_post_custom($freelancer_id);
    $freelancer_data = get_post($freelancer_id);
    $check_tabs = false;
    foreach ($custom_field_freelancer as $field) {
        if ($field['tabs'] == 'skills') {
            $check_tabs = true;
        }
    }

    if (count($custom_field_freelancer) > 0) {
        if ($check_tabs == true) : ?>
            <?php foreach ($custom_field_freelancer as $field) {
                if ($field['tabs'] == 'skills') { ?>
            <?php felan_get_template("freelancer/print/additional/field.php", array(
                        'field' => $field,
                        'freelancer_data' => $freelancer_data,
                        'freelancer_meta_data' => $freelancer_meta_data
                    ));
                }
            } ?>
    <?php endif;
    }
    ?>
</div>