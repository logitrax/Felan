<?php

/**
 * The Template for displaying company archive
 */

defined('ABSPATH') || exit;

get_header('felan');
$company_map_postion = $map_event = '';
$content_company              = felan_get_option('archive_company_layout', 'layout-list');
$enable_company_show_map = felan_get_option('enable_company_show_map', 1);
$enable_company_show_map = !empty($_GET['has_map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_map'])) : $enable_company_show_map;

$map_event = '';
if ($enable_company_show_map == 1) {
    $archive_company_filter = 'filter-canvas';
    $company_map_postion = felan_get_option('company_map_postion');
    $company_map_postion = !empty($_GET['map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['map'])) : $company_map_postion;
    if ($company_map_postion == 'map-right') {
        $map_event = 'map-event';
    }
} else {
    $archive_company_filter = felan_get_option('company_filter_sidebar_option', 'filter-left');
};
$archive_company_filter = !empty($_GET['filter']) ? Felan_Helper::felan_clean(wp_unslash($_GET['filter'])) : $archive_company_filter;
$content_company = !empty($_GET['layout']) ? Felan_Helper::felan_clean(wp_unslash($_GET['layout'])) : $content_company;
$archive_classes = array('archive-layout', 'archive-company', $archive_company_filter, $map_event, $company_map_postion);
?>

<div class="<?php echo join(' ', $archive_classes); ?>">
    <?php felan_get_template('company/archive/layout/layout-default.php'); ?>
</div>
<?php
get_footer('felan');
