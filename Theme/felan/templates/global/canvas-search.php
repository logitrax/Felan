<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
$classes = array('block-search', 'search-form-wrapper', 'canvas-search');
wp_enqueue_script('jquery-ui-autocomplete');
if (!class_exists("Felan_Framework")) {
	return;
}

$enable_post_type_jobs = \Felan_Helper::felan_get_option('enable_post_type_jobs', '1');
$enable_post_type_service = \Felan_Helper::felan_get_option('enable_post_type_service', '1');
$enable_post_type_project = \Felan_Helper::felan_get_option('enable_post_type_project', '1');
?>
<div class="<?php echo join(' ', $classes); ?>">
	<div class="bg-overlay"></div>
	<a href="#" class="btn-close"><i class="far fa-times"></i></a>
	<form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="form-search-canvas">
		<div class="jobs-search-inner">
			<div class="form-group">
				<input class="jobs-search-canvas archive-search-control" type="text" name="s" placeholder="<?php esc_attr_e('Search title or keywords', 'felan') ?>">
				<span class="btn-filter-search"><i class="far fa-search"></i></span>
			</div>
			<div class="form-group">
				<div class="select2-field">
					<select name="post_type" class="felan-select2">
						<?php if ($enable_post_type_jobs == '1') : ?>
							<option value="jobs"><?php echo esc_html__('Jobs', 'felan'); ?></option>
						<?php endif; ?>
						<?php if ($enable_post_type_service == '1') : ?>
							<option value="service"><?php echo esc_html__('Service', 'felan'); ?></option>
						<?php endif; ?>
						<?php if ($enable_post_type_project == '1') : ?>
							<option value="project"><?php echo esc_html__('Project', 'felan'); ?></option>
						<?php endif; ?>
					</select>
				</div>
				<i class="fas fa-file-alt"></i>
			</div>
			<div class="form-group location">
				<div class="select2-field">
					<select name="jobs-location" class="felan-select2">
						<?php echo '<option value="">' . esc_html__('All location', 'felan') . '</option>'; ?>
						<?php felan_get_taxonomy('felan_location', true, false); ?>
					</select>
				</div>
				<i class="fas fa-map-marker-alt"></i>
			</div>
			<div class="form-group">
				<button type="submit" class="btn-jobs-search felan-button">
					<?php esc_html_e('Search', 'felan') ?>
				</button>
			</div>
		</div>
	</form>
</div>