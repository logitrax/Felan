<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$service_id = get_the_ID();
if (!empty($service_single_id)) {
    $service_id = $service_single_id;
}
$service_categories = get_the_terms($service_id, 'service-categories');
$enable_single_service_related = felan_get_option('enable_single_service_related');

$categories = array();
if ($service_categories) :
    foreach ($service_categories as $cate) {
        $cate_id = $cate->term_id;
        $categories[] = $cate_id;
    }
endif;

$args = array(
    'posts_per_page' => 3,
    'post_type' => 'service',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'exclude' => $service_id,
    'orderby' => array(
        'menu_order' => 'ASC',
        'date' => 'DESC',
    ),
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'service-categories',
            'field' => 'id',
            'terms' => $categories
        ),
    ),
);
$get_service = get_posts($args);
?>
<?php if ($enable_single_service_related && !empty($get_service)) : ?>
    <div class="felan-block-inner block-archive-inner service-related-details">
        <div class="header-related">
            <h4 class="title-service"><?php esc_html_e('Similar Services', 'felan-framework') ?></h4>
            <a href="<?php echo get_post_type_archive_link('service') ?>" class="felan-button button-border-bottom"><?php esc_html_e('View all service', 'felan-framework') ?></a>
        </div>
        <div class="related-inner">
            <?php foreach ($get_service as $service) { ?>
                <?php felan_get_template('content-service.php', array(
                    'services_id'  => $service->ID,
                    'service_layout' => 'layout-list',
                )); ?>
            <?php } ?>
        </div>
    </div>
<?php endif; ?>