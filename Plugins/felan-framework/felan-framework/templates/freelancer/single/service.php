<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$freelancer_id     = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id;
}
$author_id = get_post_field('post_author', $freelancer_id);

$args = array(
    'posts_per_page' => 9,
    'post_type' => 'service',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'author' => $author_id,
);
$get_service = get_posts($args);
?>
<?php if (!empty($get_service)) : ?>
    <div class="felan-block-inner block-archive-inner freelancer-service-details">
        <div class="service-inner row">
            <?php foreach ($get_service as $service) { ?>
                <div class="service-item">
                    <?php felan_get_template('content-service.php', array(
                        'services_id'  => $service->ID,
                        'service_layout' => 'layout-grid',
                    )); ?>
                </div>
            <?php } ?>
        </div>
    </div>
<?php endif; ?>