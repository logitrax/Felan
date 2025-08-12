<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

global $wpdb;

$id = get_the_ID();
if (!empty($company_id)) {
	$id = $company_id;
}
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
$author_id = get_post_field('post_author', $company_id);
$company_meta_data = get_post_custom($id);
$company_location = get_the_terms($company_id, 'felan_location');
$company_location_city = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_location_city', true);
$company_categories =  get_the_terms($company_id, 'company-categories');
$company_size =  get_the_terms($company_id,  'company-size');
$company_logo   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
$company_item_class[] = 'felan-company-item';
if (!empty($layout)) {
	$company_item_class[] = $layout;
}
$company_item_class[] = 'company-' . $id;
$meta_query = felan_posts_company($id);
$enable_company_des = felan_get_option('enable_company_show_des');
$enable_company_single_popup = felan_get_option('enable_company_single_popup', '0');
$enable_company_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_company_single_popup;
$args = array(
	'posts_per_page' => -1,
	'post_type' => 'jobs',
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => FELAN_METABOX_PREFIX . 'jobs_select_company',
			'value' => $company_id,
			'compare' => '=='
		)
	)
);

$query_jobs = new WP_Query($args);
$jobs_of_company = $query_jobs->found_posts;

$args_project = array(
	'posts_per_page' => -1,
	'post_type' => 'project',
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => FELAN_METABOX_PREFIX . 'project_select_company',
			'value' => $company_id,
			'compare' => '=='
		)
	)
);

$query_project = new WP_Query($args_project);
$project_of_company = $query_project->found_posts;
?>
<div class="<?php echo join(' ', $company_item_class); ?>">
	<div class="company-top">
		<div class="company-header">
			<div class="company-header-inner">
				<a class="company-img" href="<?php echo get_the_permalink($company_id); ?>">
					<?php if (!empty($company_logo[0]['url'])) : ?>
						<img class="logo-company" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
					<?php else : ?>
						<div class="logo-company"><i class="far fa-camera"></i></div>
					<?php endif; ?>
				</a>
				<div class="company-info">
					<?php if (!empty(get_the_title($company_id))) : ?>
						<h2 class="company-title">
							<a href="<?php echo get_the_permalink($company_id); ?>"><?php echo get_the_title($company_id); ?></a>
							<?php felan_company_green_tick($company_id); ?>
						</h2>
					<?php endif; ?>
					<?php echo felan_get_total_rating('company', $company_id); ?>
				</div>
			</div>
			<div class="company-status-inner">
				<?php felan_get_template('company/follow.php', array(
					'company_id' => $company_id,
				)); ?>
			</div>
		</div>
		<div class="company-inner">
			<?php if (is_array($company_location)) { ?>
				<div class="company-location">
					<?php foreach ($company_location as $location) {
						$location_link = get_term_link($location, 'company-size'); ?>
						<a href="<?php echo esc_url($location_link); ?>" class="cate">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M12.5599 20.8207C12.2247 21.0598 11.7753 21.0598 11.4401 20.8207C6.61138 17.3773 1.48557 10.2971 6.6667 5.18128C8.08118 3.78463 9.99963 3 12 3C14.0004 3 15.9188 3.78463 17.3333 5.18128C22.5144 10.2971 17.3886 17.3773 12.5599 20.8207Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
								<path d="M12 12C13.1046 12 14 11.1046 14 10C14 8.89543 13.1046 8 12 8C10.8954 8 10 8.89543 10 10C10 11.1046 10.8954 12 12 12Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
							<?php esc_html_e($location->name); ?>
						</a>
					<?php } ?>
				</div>
			<?php } ?>
			<?php if (is_array($company_size)) { ?>
				<div class="company-size">
					<?php foreach ($company_size as $size) {
						$size_link = get_term_link($size, 'company-size'); ?>
						<a href="<?php echo esc_url($size_link); ?>" class="cate">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M17 19.5C17 17.8431 14.7614 16.5 12 16.5C9.23858 16.5 7 17.8431 7 19.5M21 16.5004C21 15.2702 19.7659 14.2129 18 13.75M3 16.5004C3 15.2702 4.2341 14.2129 6 13.75M18 9.73611C18.6137 9.18679 19 8.3885 19 7.5C19 5.84315 17.6569 4.5 16 4.5C15.2316 4.5 14.5308 4.78885 14 5.26389M6 9.73611C5.38625 9.18679 5 8.3885 5 7.5C5 5.84315 6.34315 4.5 8 4.5C8.76835 4.5 9.46924 4.78885 10 5.26389M12 13.5C10.3431 13.5 9 12.1569 9 10.5C9 8.84315 10.3431 7.5 12 7.5C13.6569 7.5 15 8.84315 15 10.5C15 12.1569 13.6569 13.5 12 13.5Z" stroke="#111111" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							</svg>
							<?php esc_html_e($size->name); ?>
						</a>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php if (!empty(get_the_content($company_id)) && $enable_company_des) : ?>
		<div class="des-company">
			<?php echo wp_trim_words(get_the_content($company_id), 15); ?>
		</div>
	<?php endif; ?>
	<?php if (is_array($company_categories)) { ?>
		<div class="company-cate">
			<?php foreach ($company_categories as $categories) {
				$cate_link = get_term_link($categories, 'company-categories'); ?>
				<a href="<?php echo esc_url($cate_link); ?>" class="label label-categories">
					<i class="far fa-tag"></i><?php esc_html_e($categories->name); ?>
				</a>
			<?php } ?>
		</div>
	<?php } ?>
	<div class="company-bottom">
		<div class="company-available">
			<?php if ($enable_post_type_jobs == '1') { ?>
				<span><?php echo esc_html($jobs_of_company) ?></span> <?php echo _n('job', 'jobs', $jobs_of_company, 'felan-framework') ?>
			<?php } ?>
			<?php if ($enable_post_type_jobs == '1' && $enable_post_type_project == '1') { ?>,<?php } ?>
			<?php if ($enable_post_type_project == '1') { ?>
				<span><?php echo esc_html($project_of_company) ?></span> <?php echo _n('project', 'projects', $project_of_company, 'felan-framework') ?>
			<?php } ?>
		</div>
	</div>
	<?php if ($enable_company_single_popup === '1' && is_archive()) { ?>
		<a class="felan-link-item btn-single-settings" data-post-id="<?php echo esc_attr($id) ?>" data-post-type="company" href="#"></a>
	<?php } ?>
</div>