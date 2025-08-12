<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$freelancer_id = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id;
}
$post_author = get_post_field('post_author', $freelancer_id);

$args_proposal = array(
    'post_type' => 'project-proposal',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => -1,
    'author' => $post_author,
    'meta_query' => array(
        'key' => FELAN_METABOX_PREFIX . 'proposal_status',
        'value' => 'completed',
        'compare' => '==',
    )
);
$project_id = array();
$data_proposal = new WP_Query($args_proposal);
if ($data_proposal->have_posts()) {
    while ($data_proposal->have_posts()) {
        $data_proposal->the_post();
        $proposal_id = get_the_ID();
        $project_id[] = get_post_meta($proposal_id, FELAN_METABOX_PREFIX . 'proposal_project_id', true);
    }
    wp_reset_postdata();
}

$args_project = array(
    'post_type' => 'project',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'post__in' => $project_id,
    'orderby' => 'date',
);

$data_project = new WP_Query($args_project);

if ($data_project->have_posts() && !empty($project_id)) { ?>
    <?php while ($data_project->have_posts()) : $data_project->the_post();
        felan_get_template('content-project.php', array(
            'project_layout' => 'layout-grid',
        ));
    endwhile; ?>
    <?php wp_reset_postdata(); ?>
<?php }