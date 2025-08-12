<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$project_id = get_the_ID();
if (!empty($project_single_id)) {
    $project_id = $project_single_id;
}
$project_categories = get_the_terms($project_id, 'project-categories');
$enable_single_project_related = felan_get_option('enable_single_project_related');

$categories = array();
if ($project_categories) :
    foreach ($project_categories as $cate) {
        $cate_id = $cate->term_id;
        $categories[] = $cate_id;
    }
endif;

$args = array(
    'posts_per_page' => 3,
    'post_type' => 'project',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'exclude' => $project_id,
    'orderby' => array(
        'menu_order' => 'ASC',
        'date' => 'DESC',
    ),
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'project-categories',
            'field' => 'id',
            'terms' => $categories
        ),
    ),
);
$get_project = get_posts($args);
?>
<?php if ($enable_single_project_related && !empty($get_project)) : ?>
    <div class="felan-block-inner block-archive-inner project-related-details">
        <div class="header-related">
            <h4 class="title-project"><?php esc_html_e('Similar Project', 'felan-framework') ?></h4>
            <a href="<?php echo get_post_type_archive_link('project') ?>" class="felan-button button-border-bottom"><?php esc_html_e('View all project', 'felan-framework') ?></a>
        </div>
        <div class="related-inner">
            <?php foreach ($get_project as $project) { ?>
                <?php felan_get_template('content-project.php', array(
                    'projects_id'  => $project->ID,
                    'project_layout' => 'layout-list',
                )); ?>
            <?php } ?>
        </div>
    </div>
<?php endif; ?>