<?php

/**
 * The Template for displaying freelancer archive
 */

defined('ABSPATH') || exit;

get_header('felan');
$map_event = $freelancer_map_postion = '';
$content_freelancer = felan_get_option('archive_freelancer_layout', 'layout-list');
$enable_freelancer_show_map = felan_get_option('enable_freelancer_show_map', 1);
$enable_freelancer_show_map = !empty($_GET['has_map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_map'])) : $enable_freelancer_show_map;

if ($enable_freelancer_show_map == 1) {
    $archive_freelancer_filter = 'filter-canvas';
    $freelancer_map_postion = felan_get_option('freelancer_map_postion');
    $freelancer_map_postion = !empty($_GET['map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['map'])) : $freelancer_map_postion;
    if ($freelancer_map_postion == 'map-right') {
        $map_event = 'map-event';
    }
} else {
    $archive_freelancer_filter = felan_get_option('freelancer_filter_sidebar_option', 'filter-left');
};
$archive_freelancer_filter = !empty($_GET['filter']) ? Felan_Helper::felan_clean(wp_unslash($_GET['filter'])) : $archive_freelancer_filter;
$content_freelancer = !empty($_GET['layout']) ? Felan_Helper::felan_clean(wp_unslash($_GET['layout'])) : $content_freelancer;
$archive_classes = array('archive-layout', 'archive-freelancers', $archive_freelancer_filter, $map_event, $freelancer_map_postion);

?>
<div class="<?php echo join(' ', $archive_classes); ?>">
    <?php felan_get_template('freelancer/archive/layout/layout-default.php'); ?>
</div>
<?php
get_footer('felan');
