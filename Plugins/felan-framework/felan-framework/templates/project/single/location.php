<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$project_id = get_the_ID();
if (!empty($project_single_id)) {
    $project_id = $project_single_id;
}
$project_location = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_location', true);
$project_address = get_post_meta($project_id, FELAN_METABOX_PREFIX . 'project_address');
$project_address = isset($project_address) ? $project_address[0] : '';
$map_type = felan_get_option('map_type', 'mapbox');
if (!empty($project_location['location']) && !empty($project_address)) {
    list($lat, $lng) = !empty($project_location['location']) ? explode(',', $project_location['location']) : array('', '');
} else {
    return;
}
felan_get_single_map_type($lng, $lat);

$enable_project_single_popup = felan_get_option('enable_project_single_popup', '0');
$enable_project_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_project_single_popup;
if($enable_project_single_popup = '1'){
    return;
}
?>
<div class="felan-block-inner block-archive-inner project-maps-details">
    <h4 class="title-project"><?php esc_html_e('Location', 'felan-framework') ?></h4>
    <div class="entry-detail">
        <?php if ($map_type == 'google_map') { ?>
            <div id="google_map" class="felan-map-warpper"></div>
        <?php } else if ($map_type == 'openstreetmap') { ?>
            <div id="openstreetmap_map" class="felan-map-warpper"></div>
        <?php } else { ?>
            <div id="mapbox_map" class="felan-map-warpper"></div>
        <?php } ?>
    </div>
</div>