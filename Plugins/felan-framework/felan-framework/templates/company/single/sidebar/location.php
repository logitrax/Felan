<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$company_id    = get_the_ID();
if (!empty($company_single_id)) {
    $company_id = $company_single_id['post_id'];
}
$company_location = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'felan_location', true);

$map_type = felan_get_option('map_type', 'mapbox');
if (!empty($company_location['location'])) {
    list($lat, $lng) = !empty($company_location['location']) ? explode(',', $company_location['location']) : array('', '');
    $map_direction = "http://maps.google.com/?q=" . $company_location['location'];
} else {
    return;
}

$classes = array();
$enable_sticky_sidebar_type = felan_get_option('enable_sticky_company_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
}

felan_get_single_map_type($lng, $lat);

$enable_company_single_popup = felan_get_option('enable_company_single_popup', '0');
$enable_company_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_company_single_popup;
if ($enable_company_single_popup = '1') {
    return;
}
?>
<div class="block-archive-sidebar company-sidebar <?php echo implode(" ", $classes); ?>">
    <div class="felan-location">
        <h3><?php esc_html_e('Location', 'felan-framework') ?></h3>
        <a href="<?php echo esc_url($map_direction) ?>" class="felan-button button-border-bottom" target="_blank"><?php esc_html_e('Get direction', 'felan-framework') ?></a>
    </div>
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