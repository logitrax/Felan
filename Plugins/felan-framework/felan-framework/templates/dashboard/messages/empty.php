<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$mess_empty = FELAN_PLUGIN_URL . 'assets/images/mess-empty.svg';

$link_user = $user_name = '';
if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
    $user_name = esc_html__( 'companies', 'felan-framework' );
    $link_user = get_post_type_archive_link('company');
} elseif (in_array("felan_user_employer", (array)$current_user->roles)) {
    $user_name = esc_html__( 'freelancer', 'felan-framework' );
    $link_user = get_post_type_archive_link('freelancer');
}
?>
<div class="mess-list empty">
    <div class="tab-mess">
        <ul class="tab-list-mess">
            <li class="tab-item tab-all"><a href="#tab-all"><?php esc_html_e('All', 'felan-framework'); ?></a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-info" id="tab-all">
                <p class="no-mess"><?php esc_html_e('You have no messages', 'felan-framework'); ?></p>
            </div>
        </div>
    </div>
</div>
<div class="mess-content empty">
    <img src="<?php echo esc_url($mess_empty); ?>" alt="" />
    <h2><?php esc_html_e('Welcome to Messages', 'felan-framework'); ?></h2>
    <p><?php echo sprintf(esc_html__('When you contact a %s, it will appear here.', 'felan-framework'), $user_name) ?></p>
    <a href="<?php echo esc_url($link_user); ?>" target="_blank" class="felan-button"><?php echo sprintf(esc_html__('Find %s', 'felan-framework'), $user_name) ?></a>
</div>