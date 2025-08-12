<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly 
}
$hide_freelancer_fields = felan_get_option('hide_freelancer_fields', array());
if (!is_array($hide_freelancer_fields)) {
    $hide_freelancer_fields = array();
}
$layout_info = array('resume', 'social', 'gallery', 'video');
?>

<div id="tab-info" class="tab-info">
    <?php felan_get_template('dashboard/freelancer/profile/info/basic.php') ?>
    <?php felan_get_template('dashboard/freelancer/profile/info/location.php') ?>
    <?php foreach ($layout_info as $value) {
        switch ($value) {
            case 'resume':
                break;
            case 'social':
                break;
            case 'gallery':
                break;
            case 'video':
                break;
        }
        if (!in_array('fields_freelancer_' . $value, $hide_freelancer_fields)) : ?>
            <?php felan_get_template('dashboard/freelancer/profile/info/' . $value . '.php') ?>
    <?php endif;
    } ?>
    <?php felan_custom_field_freelancer('info'); ?>
</div>