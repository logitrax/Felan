<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$service_id = get_the_ID();
if (!empty($service_single_id)) {
    $service_id = $service_single_id;
}
$service_location = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_location', true);
$service_address = get_post_meta($service_id, FELAN_METABOX_PREFIX . 'service_address');
$service_address = isset($service_address) ? $service_address[0] : '';
$map_type = felan_get_option('map_type', 'mapbox');
if (!empty($service_location['location']) && !empty($service_address)) {
    list($lat, $lng) = !empty($service_location['location']) ? explode(',', $service_location['location']) : array('', '');
} else {
    return;
}
felan_get_single_map_type($lng, $lat);

$enable_service_single_popup = felan_get_option('enable_service_single_popup', '0');
$enable_service_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_service_single_popup;
if($enable_service_single_popup = '1'){
    return;
}
?>
<div class="felan-block-inner block-archive-inner service-maps-details">
    <h4 class="title-service"><?php esc_html_e('Location', 'felan-framework') ?></h4>
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