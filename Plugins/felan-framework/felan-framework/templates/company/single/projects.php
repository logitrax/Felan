<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$company_id = get_the_ID();
if (!empty($company_single_id)) {
    $company_id = $company_single_id;
}
$author_id = get_post_field('post_author', $company_id);
$args_project = array(
    'post_type' => 'project',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => -1,
    'author' => $author_id,
    'orderby' => 'date',
    'meta_query' => array(
        array(
            'key' => FELAN_METABOX_PREFIX . 'project_select_company',
            'value' => $company_id,
            'compare' => '==',
        ),
    ),
);

$data_project = new WP_Query($args_project);

if ($data_project->have_posts()) { ?>
    <?php while ($data_project->have_posts()) : $data_project->the_post();
        felan_get_template('content-project.php', array(
            'project_layout' => 'layout-grid',
        ));
    endwhile; ?>
    <?php wp_reset_postdata(); ?>
<?php } else { ?>
    <p><?php echo esc_html__('No projects found', 'felan-framework'); ?></p>
<?php }
