<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_freelancer_fields, $current_user, $freelancer_data;
$user_id = $current_user->ID;

$freelancer_location = get_post_meta($freelancer_data->ID, FELAN_METABOX_PREFIX . 'freelancer_location', true);
$freelancer_map_address = isset($freelancer_location['address']) ? $freelancer_location['address'] : '';
$freelancer_map_location = isset($freelancer_location['location']) ? $freelancer_location['location'] : '';

$map_type = felan_get_option('map_type', 'mapbox');
$map_default_position = felan_get_option('map_default_position', '');
$lat = felan_get_option('map_lat_default', '59.325');
$lng = felan_get_option('map_lng_default', '18.070');
if (!empty($freelancer_location['location'])) {
    list($lat, $lng) = !empty($freelancer_location['location']) ? explode(',', $freelancer_location['location']) : array('', '');
} else {
    if ($map_default_position) {
        if ($map_default_position['location']) {
            list($lat, $lng) = !empty($map_default_position['location']) ? explode(',', $map_default_position['location']) : array('', '');
        }
    }
}

felan_get_map_type($lng, $lat, '#freelancer-profile-form');

?>
<?php if (!in_array('fields_freelancer_location', $hide_freelancer_fields) || !in_array('fields_freelancer_map', $hide_freelancer_fields)) : ?>
    <div class="freelancer-submit-location block-from felan-map-form" id="submit_freelancer_form">
        <h6><?php esc_html_e('Location', 'felan-framework') ?></h6>
        <div class="row">
            <?php if (!in_array('fields_freelancer_location', $hide_freelancer_fields)) : ?>
                <div class="form-group col-lg-6">
                    <label><?php esc_html_e('Location', 'felan-framework') ?></label>
                    <div class="select2-field">
                        <select name="freelancer_location" class="felan-select2 point-mark">
                            <?php felan_get_taxonomy_location('felan_location', 'felan_state', 'felan-location-state', 'felan-state-country', $freelancer_data->ID); ?>
                        </select>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!in_array('fields_freelancer_map', $hide_freelancer_fields)) : ?>
                <div class="form-group col-lg-6">
                    <label for="search-location"><?php esc_html_e('Maps location', 'felan-framework') ?></label>
                    <input type="text" id="search-location" class="form-control" name="felan_map_address" value="<?php echo esc_attr($freelancer_map_address); ?>" placeholder="<?php esc_attr_e('Full Address', 'felan-framework'); ?>" autocomplete="off">
                    <input type="hidden" class="form-control freelancer-map-location" name="felan_map_location" value="<?php echo esc_attr($freelancer_map_location); ?>" />
                    <div id="geocoder" class="geocoder"></div>
                </div>
                <div class="form-group col-md-12 freelancer-fields-map">
                    <div class="freelancer-fields freelancer-map">
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
                    <label for="freelancer_longtitude"><?php esc_html_e('Longtitude', 'felan-framework'); ?></label>
                    <input type="text" id="freelancer_longtitude" name="felan_longtitude" value="<?php echo $lng ?>" placeholder="<?php esc_attr_e('0.0000000', 'felan-framework') ?>">
                </div>
                <div class="form-group col-md-6">
                    <label for="freelancer_latitude"><?php esc_html_e('Latitude', 'felan-framework'); ?></label>
                    <input type="text" id="freelancer_latitude" name="felan_latitude" value="<?php echo $lat ?>" placeholder="<?php esc_attr_e('0.0000000', 'felan-framework') ?>">
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>