<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
?>
<?php if (!empty($data_notification)) { ?>
    <div class="head-noti">
        <div class="head-left">
            <span class="noti-refresh">
                <i class="far fa-sync"></i>
                <?php esc_html_e('Refresh', 'felan-framework'); ?>
            </span>
            <span class="noti-clear">
                <i class="far fa-trash-alt"></i>
                <?php esc_html_e('Clear All', 'felan-framework'); ?>
            </span>
        </div>
        <a href="#" class="close-noti">
            <i class="far fa-times"></i>
        </a>
    </div>
    <ul>
        <?php foreach ($data_notification as $data) {
            $post_id = $data->ID;
            $user_send_id = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'user_send_noti', true);
            $user_avatar = get_the_author_meta('author_avatar_image_url', $user_send_id);
            $user_send = get_the_author_meta('display_name', $user_send_id);
            $time = human_time_diff(get_the_time('U', $post_id), current_time('timestamp'));
            $mess_noti = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'mess_noti', true);

            $link = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'link_post_noti', true);
            if (!empty($link)) {
                $link_noti = '<a href="' . $link . '">' . get_the_title($post_id) . '</a>';
            } else {
                $link_noti = '';
            }

            $page_link = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'link_page_noti', true);
            if (!empty($page_link)) {
                $link_page = $page_link;
            } else {
                $link_page = '#';
            }

        ?>
            <li>
                <?php if (!empty($user_avatar)) : ?>
                    <img class="avatar" src="<?php echo esc_attr($user_avatar) ?>" alt="" />
                <?php else : ?>
                    <span class="avatar"><i class="far fa-camera"></i></span>
                <?php endif; ?>
                <span class="content-wrapper">
                    <span class="content">
                        <?php
						$noti_action = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'action_noti', true);
						if ( $noti_action == 'Proposal' ) {
							$link_noti = get_the_title($post_id);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has submitted a proposal for your project <b>' . $link_noti . '.</b></div>',
							);
						} elseif ( $noti_action == 'Service Order' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has placed a new order for your service <b>' . $link_noti . '.</b></div>',
							);
						} elseif ( $noti_action == 'Dispute' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has approved dispute for your project <b>' . $link_noti . '.</b></div>',
							);
						} elseif ( $noti_action == 'Dispute denied' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has denied dispute for your project <b>' . $link_noti . '.</b></div>',
							);
						} elseif ( $noti_action == 'Dispute service' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has approved dispute for your service <b>' . $link_noti . '.</b></div>',
							);
						} elseif ( $noti_action == 'Dispute denied service' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has denied dispute for your service <b>' . $link_noti . '.</b></div>',
							);
						} elseif ( $noti_action == 'Message Dispute Project' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has sent a new message in the dispute for project <b>' . $link_noti . '.</b></div>',
							);
						} elseif ( $noti_action == 'Message Dispute service' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has sent a new message in the dispute for service <b>' . $link_noti . '.</b></div>',
							);
						} elseif ( $noti_action == 'Employer Approve Proposal' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has approved your proposal for project <b>' . $link_noti . '.</b></div>',
							);
						} elseif ( $noti_action == 'Employer Reject Proposal' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has rejected your proposal for project <b>' . $link_noti . '.</b></div>',
							);
						} elseif ( $noti_action == 'Employer Create Dispute Proposal' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has create dispute your proposal for project <b>' . $link_noti . '.</b></div>',
							);
						} elseif ( $noti_action == 'Message Service' ) {
							$link_noti = get_the_title($link);

							$message_template = __('<b>%1$s</b> has sent a new message in service <b>%2$s</b>', 'felan-framework');
							$message_html = sprintf($message_template, $user_send, $link_noti);

							echo '<div class="message" style="font-size: 12px;">' . $message_html . '</div>';

						} elseif ( $noti_action == 'Service Completed' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has marked the service <b>' . $link_noti . '</b> as completed.</div>',
							);
						} elseif ( $noti_action == 'Service Canceled' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has marked the service <b>' . $link_noti . '</b> as canceled.</div>',
							);
						} elseif ( $noti_action == 'Employer Create Dispute Service' ) {
							$link_noti = get_the_title($link);
							echo sprintf(
								esc_html__('%1s %2s', 'felan-framework'),
								$mess_noti,
								'<div class="message" style="font-size: 12px;"><b>' . $user_send . '</b> has create dispute for service <b>' . $link_noti . '.</b></div>',
							);
						} else {
							echo sprintf(
								esc_html__('%1s %2s by %3s', 'felan-framework'),
								$mess_noti,
								'<b>' . $link_noti . '</b>',
								'<b>' . $user_send . '</b>'
							);
						}
						?>
                    </span>
                    <span class="date">
                        <?php echo sprintf(esc_html__('%s ago', 'felan-framework'), $time); ?>
                    </span>
                </span>
                <span class="action action-setting">
                    <a href="#" class="icon-setting"><i class="far fa-ellipsis-v"></i></a>
                    <span class="action-dropdown">
                        <a class="btn-delete" data-noti-id="<?php echo esc_attr(wp_json_encode($post_id)); ?>" href="#"><?php esc_html_e('Delete', 'felan-framework') ?></a>
                    </span>
                </span>
                <a href="<?php echo $link_page ?>" class="link-page"></a>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <span class="empty"><?php esc_html_e('You do not have any notifications.', 'felan-framework'); ?></span>
<?php } ?>