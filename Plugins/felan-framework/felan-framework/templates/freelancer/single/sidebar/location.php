<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$freelancer_id    = get_the_ID();
if (!empty($freelancer_single_id)) {
    $freelancer_id = $freelancer_single_id['post_id'];
}
$freelancer_location = get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_location', true);
$map_type = felan_get_option('map_type', 'mapbox');
if (!empty($freelancer_location['location'])) {
    list($lat, $lng) = !empty($freelancer_location['location']) ? explode(',', $freelancer_location['location']) : array('', '');
    $map_direction = "http://maps.google.com/?q=" . $freelancer_location['location'];
} else {
    return;
}

$classes = array();
$enable_sticky_sidebar_type = felan_get_option('enable_sticky_freelancer_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
}

felan_get_single_map_type($lng, $lat);

$enable_freelancer_single_popup = felan_get_option('enable_company_single_popup', '0');
$enable_freelancer_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_freelancer_single_popup;
if($enable_freelancer_single_popup = '1'){
    return;
}
?>
<div class="block-archive-sidebar freelancer-sidebar <?php echo implode(" ", $classes); ?>">
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