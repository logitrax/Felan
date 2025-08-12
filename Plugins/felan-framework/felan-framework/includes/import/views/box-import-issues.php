<?php

/**
 * Issues Box
 *
 * @package Felan_Framework
 */

if (!defined('ABSPATH')) {
	exit();
}

?>
<div class="felan-box felan-box--red felan-box--import-issues">
	<div class="felan-box__header">
		<span class="felan-box__icon"><i class="fad fa-exclamation-triangle"></i></span>
		<span><?php esc_html_e('Issues Detected', 'felan-framework'); ?></span>
	</div>
	<div class="felan-box__body">

		<?php
		/**
		 * Hook: felan_box_import_issues_before_content
		 */
		do_action('felan_box_import_issues_before_content');
		?>

		<ol>
			<?php foreach ($import_issues as $issue) : ?>
				<li><?php echo wp_kses_post($issue); ?></li>
			<?php endforeach; ?>
		</ol>

		<?php
		/**
		 * Hook: felan_box_import_issues_after_content
		 */
		do_action('felan_box_import_issues_after_content');
		?>

	</div>
	<div class="felan-box__footer">
		<span style="color: #dc433f">
			<?php esc_html_e('Please solve all issues listed above before importing demo data.', 'felan-framework'); ?>
		</span>
	</div>
</div>