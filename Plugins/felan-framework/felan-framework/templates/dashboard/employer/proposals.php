<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$project_id = isset($_GET['project_id']) ? felan_clean(wp_unslash($_GET['project_id'])) : '';
$applicants_id = isset($_GET['applicants_id']) ? felan_clean(wp_unslash($_GET['applicants_id'])) : '';
$pages = isset($_GET['pages']) ? felan_clean(wp_unslash($_GET['pages'])) : '';
$projects_submit = felan_get_permalink('projects_submit');
?>

<div class="felan-employer-service entry-my-page">
	<?php felan_withdraw_noti_print('featured_project','You will be charged %s for a featured project.'); ?>
	<div class="entry-title">
		<h4><?php esc_html_e('Proposals', 'felan-framework'); ?></h4>
	</div>
	<?php felan_get_template('dashboard/employer/project/proposals.php'); ?>
</div>
