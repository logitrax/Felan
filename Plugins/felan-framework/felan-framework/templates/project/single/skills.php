<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$project_id = get_the_ID();
if (!empty($project_single_id)) {
    $project_id = $project_single_id;
}
$project_skills = get_the_terms($project_id, 'project-skills');
?>
<?php if (is_array($project_skills)) { ?>
    <div class="felan-block-inner block-archive-inner project-skills-details">
        <h4 class="title-project"><?php esc_html_e('Skills', 'felan-framework') ?></h4>
        <div class="skills-warpper">
            <?php foreach ($project_skills as $skills) {
                if ($skills->term_id !== '') {
                    $skills_link = get_term_link($skills, 'project-skills');
            ?>
                    <a class="label label-skills" href="<?php echo esc_url($skills_link); ?>">
                        <?php echo $skills->name; ?>
                    </a>
            <?php }
            } ?>
        </div>
    </div>
<?php } ?>