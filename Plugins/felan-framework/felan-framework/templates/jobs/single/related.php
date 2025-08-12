<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$jobs_id = get_the_ID();
if (!empty($job_id)) {
    $jobs_id = $job_id;
}
$jobs_categories = get_the_terms($jobs_id, 'jobs-categories');
$enable_single_jobs_related = felan_get_option('enable_single_jobs_related', '1');

$categories = array();
if ($jobs_categories) :
    foreach ($jobs_categories as $cate) {
        $cate_id = $cate->term_id;
        $categories[] = $cate_id;
    }
endif;

$args = array(
    'posts_per_page' => 3,
    'post_type' => 'jobs',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'exclude' => $jobs_id,
    'orderby' => array(
        'menu_order' => 'ASC',
        'date' => 'DESC',
    ),
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'jobs-categories',
            'field' => 'id',
            'terms' => $categories
        ),
        'meta_query' => array(
            array(
                'key' => FELAN_METABOX_PREFIX . 'enable_jobs_package_expires',
                'value' => 0,
                'compare' => '=='
            )
        ),
    ),
);
$jobs = get_posts($args);
?>
<?php if ($enable_single_jobs_related && !empty($jobs)) : ?>
    <div class="jobs-related-details">
        <div class="header-related">
            <h4 class="title-jobs"><?php esc_html_e('Similar jobs', 'felan-framework') ?></h4>
            <a href="<?php echo get_post_type_archive_link('jobs') ?>" class="felan-button button-border-bottom"><?php esc_html_e('View all jobs', 'felan-framework') ?></a>
        </div>
        <div class="related-inner">
            <?php echo felan_get_jobs_by_category(3, 3, $categories,$jobs_id); ?>
        </div>
    </div>
<?php endif; ?>