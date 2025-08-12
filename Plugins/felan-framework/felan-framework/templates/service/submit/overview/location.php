<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_service_fields, $current_user;
$user_id = $current_user->ID;

$map_type = felan_get_option('map_type', 'mapbox');
$map_default_position = felan_get_option('map_default_position', '');
$lat = felan_get_option('map_lat_default', '59.325');
$lng = felan_get_option('map_lng_default', '18.070');
if (!empty($service_location['location'])) {
    list($lat, $lng) = !empty($service_location['location']) ? explode(',', $service_location['location']) : array('', '');
} else {
    if ($map_default_position) {
        if ($map_default_position['location']) {
            list($lat, $lng) = !empty($map_default_position['location']) ? explode(',', $map_default_position['location']) : array('', '');
        }
    }
}

felan_get_map_type($lng, $lat, '#submit_service_form');

?>
<?php if (!in_array('fields_service_location', $hide_service_fields)) : ?>
    <div class="row">
        <?php if (!in_array('fields_service_location', $hide_service_fields)) : ?>
            <div class="form-group col-lg-6">
                <label><?php esc_html_e('Location', 'felan-framework') ?></label>
                <div class="select2-field">
                    <select name="service_location" class="felan-select2">
                        <?php felan_get_taxonomy_location('felan_location', 'felan_state', 'felan-location-state', 'felan-state-country'); ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!in_array('fields_service_map', $hide_service_fields)) : ?>
            <div class="form-group col-lg-6">
                <label for="search-location"><?php esc_html_e('Maps location', 'felan-framework') ?></label>
                <div class="input-area">
                    <input type="text" id="search-location" class="form-control" name="felan_map_address" placeholder="<?php esc_attr_e('Full Address', 'felan-framework'); ?>" autocomplete="off">
                </div>
                <input type="hidden" class="form-control service-map-location" name="felan_map_location" />
                <div id="geocoder" class="geocoder"></div>
            </div>
            <div class="form-group col-md-12 service-fields-map">
                <div class="service-fields service-map">
                    <?php if ($map_type == 'google_map') { ?>
                        <div class="map_canvas maptype felan-map-warpper" id="map"></div>
                    <?php } else if ($map_type == 'openstreetmap') { ?>
                        <div id="openstreetmap_location" class="felan-map-warpper"></div>
                    <?php } else { ?>
                        <div id="mapbox_location" class="felan-map-warpper"></div>
                    <?php } ?>
                </div>
            </div>
            <div class="form-group col-md-6">
                <label for="service_longtitude"><?php esc_html_e('Longtitude', 'felan-framework'); ?></label>
                <input type="text" id="service_longtitude" name="felan_longtitude" value="<?php echo $lng ?>" placeholder="<?php esc_attr_e('0.0000000', 'felan-framework') ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="service_latitude"><?php esc_html_e('Latitude', 'felan-framework'); ?></label>
                <input type="text" id="service_latitude" name="felan_latitude" value="<?php echo $lat ?>" placeholder="<?php esc_attr_e('0.0000000', 'felan-framework') ?>">
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>