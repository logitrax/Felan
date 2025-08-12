<?php

/**
 * The Template for displaying project archive
 */

defined('ABSPATH') || exit;

get_header('felan');
$project_map_postion = $map_event = '';
$content_project              = felan_get_option('archive_project_layout', 'layout-list');
$enable_project_show_map = felan_get_option('enable_project_show_map', 1);
$enable_project_show_map = !empty($_GET['has_map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_map'])) : $enable_project_show_map;

$map_event = '';
if ($enable_project_show_map == 1) {
    $archive_project_filter = 'filter-canvas';
    $project_map_postion = felan_get_option('project_map_postion');
    $project_map_postion = !empty($_GET['map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['map'])) : $project_map_postion;
    if ($project_map_postion == 'map-right') {
        $map_event = 'map-event';
    }
} else {
    $archive_project_filter = felan_get_option('project_filter_sidebar_option', 'filter-left');
};
$archive_project_filter = !empty($_GET['filter']) ? Felan_Helper::felan_clean(wp_unslash($_GET['filter'])) : $archive_project_filter;
$content_project = !empty($_GET['layout']) ? Felan_Helper::felan_clean(wp_unslash($_GET['layout'])) : $content_project;
$archive_classes = array('archive-layout', 'archive-project', $archive_project_filter, $map_event, $project_map_postion);
?>

<div class="<?php echo join(' ', $archive_classes); ?>">
    <?php felan_get_template('project/archive/layout/layout-default.php'); ?>
</div>
<?php
get_footer('felan');
