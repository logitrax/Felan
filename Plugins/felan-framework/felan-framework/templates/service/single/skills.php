<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$service_id = get_the_ID();
if (!empty($service_single_id)) {
    $service_id = $service_single_id;
}
$service_skills = get_the_terms($service_id, 'service-skills');
?>
<?php if (is_array($service_skills)) { ?>
    <div class="felan-block-inner block-archive-inner service-skills-details">
        <h4 class="title-service"><?php esc_html_e('Skills', 'felan-framework') ?></h4>
        <div class="skills-warpper">
            <?php foreach ($service_skills as $skills) {
                if ($skills->term_id !== '') {
                    $skills_link = get_term_link($skills, 'service-skills');
            ?>
                    <a class="label label-skills" href="<?php echo esc_url($skills_link); ?>">
                        <?php echo $skills->name; ?>
                    </a>
            <?php }
            } ?>
        </div>
    </div>
<?php } ?>