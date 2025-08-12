<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_project_fields, $current_user, $project_data;
$user_id = $current_user->ID;

$project_location = get_post_meta($project_data->ID, FELAN_METABOX_PREFIX . 'project_location', true);
$project_map_address = isset($project_location['address']) ? $project_location['address'] : '';
$project_map_location = isset($project_location['location']) ? $project_location['location'] : '';

$map_type = felan_get_option('map_type', 'mapbox');
$map_default_position = felan_get_option('map_default_position', '');
$lat = felan_get_option('map_lat_default', '59.325');
$lng = felan_get_option('map_lng_default', '18.070');
if (!empty($project_location['location'])) {
    list($lat, $lng) = !empty($project_location['location']) ? explode(',', $project_location['location']) : array('', '');
} else {
    if ($map_default_position) {
        if ($map_default_position['location']) {
            list($lat, $lng) = !empty($map_default_position['location']) ? explode(',', $map_default_position['location']) : array('', '');
        }
    }
}
felan_get_map_type($lng, $lat, '#submit_project_form');
?>
<?php if (!in_array('fields_project_location', $hide_project_fields)) : ?>
    <div class="row">
        <?php if (!in_array('fields_project_location', $hide_project_fields)) : ?>
            <div class="form-group col-md-6">
                <label><?php esc_html_e('Location', 'felan-framework') ?></label>
                <div class="select2-field">
                    <select name="project_location" class="felan-select2">
                        <?php felan_get_taxonomy_location('felan_location', 'felan_state', 'felan-location-state', 'felan-state-country', $project_data->ID); ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!in_array('fields_project_map', $hide_project_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="search-location"><?php esc_html_e('Maps location', 'felan-framework') ?></label>
                <input type="text" id="search-location" class="form-control" name="felan_map_address" value="<?php echo esc_attr($project_map_address); ?>" placeholder="<?php esc_attr_e('Full Address', 'felan-framework'); ?>" autocomplete="off">
                <input type="hidden" class="form-control project-map-location" name="felan_map_location" value="<?php echo esc_attr($project_map_location); ?>" />
                <div id="geocoder" class="geocoder"></div>
            </div>

            <div class="form-group col-md-12 project-fields-map">
                <div class="project-fields project-map">
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
                <label for="project_longtitude"><?php esc_html_e('Longtitude', 'felan-framework'); ?></label>
                <input type="text" id="project_longtitude" name="felan_longtitude" value="<?php echo $lng ?>" placeholder="<?php esc_attr_e('0.0000000', 'felan-framework') ?>">
            </div>
            <div class="form-group col-md-6">
                <label for="project_latitude"><?php esc_html_e('Latitude', 'felan-framework'); ?></label>
                <input type="text" id="project_latitude" name="felan_latitude" value="<?php echo $lat ?>" placeholder="<?php esc_attr_e('0.0000000', 'felan-framework') ?>">
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>