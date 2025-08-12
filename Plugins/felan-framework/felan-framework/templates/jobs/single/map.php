<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$jobs_id = get_the_ID();
if (!empty($job_id)) {
    $jobs_id = $job_id;
}
$jobs_location = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_location', true);
$jobs_address = get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_address');
$jobs_address = !empty($jobs_address) ? $jobs_address[0] : '';
$map_type = felan_get_option('map_type', 'mapbox');
if (!empty($jobs_location['location']) && !empty($jobs_address)) {
    list($lat, $lng) = !empty($jobs_location['location']) ? explode(',', $jobs_location['location']) : array('', '');
} else {
    return;
}
felan_get_single_map_type($lng, $lat);

$enable_jobs_single_popup = felan_get_option('enable_jobs_single_popup', '0');
$enable_jobs_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_jobs_single_popup;
if($enable_jobs_single_popup = '1'){
    return;
}
?>
<div class="block-archive-inner jobs-maps-details">
    <h4 class="title-jobs"><?php esc_html_e('Maps', 'felan-framework') ?></h4>
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