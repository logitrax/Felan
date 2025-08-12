<?php

/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */
?>

<aside id="secondary" class="widget-area">

	<div class="widget-area-init">

		<?php
		if (is_single() && (get_post_type() == 'jobs')) :
			do_action('felan_single_jobs_sidebar');
		endif;
		?>
		<?php
		if (is_single() && (get_post_type() == 'company')) :
			do_action('felan_single_company_sidebar');
		endif;
		?>
		<?php
		if (is_single() && (get_post_type() == 'freelancer')) :
			do_action('felan_single_freelancer_sidebar');
		endif;
		?>
		<?php
		if (is_single() && (get_post_type() == 'service')) :
			do_action('felan_single_service_sidebar');
		endif;
		?>
		<?php
		if (is_single() && (get_post_type() == 'project')) :
			do_action('felan_single_project_sidebar');
		endif;
		?>

		<?php
		$sidebar_name = '';
		if (get_post_type() == 'jobs') {
			$sidebar_name = 'jobs_sidebar';
		} elseif (get_post_type() == 'company') {
			$sidebar_name = 'company_sidebar';
		} elseif (get_post_type() == 'freelancer') {
			$sidebar_name = 'freelancer_sidebar';
		} elseif (get_post_type() == 'service') {
			$sidebar_name = 'service_sidebar';
		} elseif (get_post_type() == 'project') {
			$sidebar_name = 'project_sidebar';
		}
		if ($sidebar_name) {
			if (is_active_sidebar($sidebar_name)) {
				dynamic_sidebar($sidebar_name);
			}
		} else {
			dynamic_sidebar('sidebar');
		}
		?>

	</div>

</aside>