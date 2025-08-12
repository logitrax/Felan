<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
if (
    in_array('felan_user_freelancer', (array)$current_user->roles)
    || in_array('felan_user_employer', (array)$current_user->roles)
) {
    wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'notification');
}
$data_notification = felan_get_data_notification();
?>
<div class="felan-notification">

    <?php felan_get_template('dashboard/notification/count.php', array(
        'data_notification' => $data_notification,
    )); ?>

    <?php if (
        in_array('felan_user_freelancer', (array)$current_user->roles)
        || in_array('felan_user_employer', (array)$current_user->roles)
    ) { ?>
        <div class="content-noti custom-scrollbar">
            <?php felan_get_template('dashboard/notification/content.php', array(
                'data_notification' => $data_notification,
            )); ?>
        </div>
    <?php } ?>

</div>