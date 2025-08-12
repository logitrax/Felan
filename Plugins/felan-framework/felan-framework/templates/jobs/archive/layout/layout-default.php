<?php

/**
 * The Template for displaying jobs archive
 */

defined('ABSPATH') || exit;

wp_enqueue_script('plupload');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'select-location');
$freelancer_resume = isset($freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_resume_id_list']) ? $freelancer_meta_data[FELAN_METABOX_PREFIX . 'freelancer_resume_id_list'][0] : '';
$filename = basename(get_attached_file($freelancer_resume));
$ajax_url = admin_url('admin-ajax.php');
$cv_file = felan_get_option('felan-cv-type');
$cv_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');

$upload_nonce = wp_create_nonce('felan_thumbnail_allow_upload');
$url = FELAN_AJAX_URL . '?action=felan_thumbnail_upload_ajax&nonce=' . esc_attr($upload_nonce);
$text = '<i class="far fa-arrow-from-bottom large"></i> ' . esc_html__('Browse', 'felan-framework');

wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'upload-cv');

wp_localize_script(
	FELAN_PLUGIN_PREFIX . 'upload-cv',
	'felan_upload_cv_vars',
	array(
		'ajax_url' => $ajax_url,
		'title' => esc_html__('Valid file formats', 'felan-framework'),
		'cv_file' => $cv_file,
		'cv_max_file_size' => $cv_max_file_size,
		'upload_nonce' => $upload_nonce,
		'url' => $url,
		'text' => $text,
	)
);
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'jobs-archive');

$enable_jobs_single_popup = felan_get_option('enable_jobs_single_popup', '0');
$enable_jobs_single_popup = !empty($_GET['has_popup']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_popup'])) : $enable_jobs_single_popup;
$items_amount = felan_get_option('archive_jobs_items_amount', '12');
$content_jobs = felan_get_option('archive_jobs_layout', 'layout-list');
$content_jobs = !empty($_GET['layout']) ? Felan_Helper::felan_clean(wp_unslash($_GET['layout'])) : $content_jobs;
$hide_jobs_top_filter_fields = felan_get_option('hide_jobs_top_filter_fields');
$enable_jobs_filter_top = felan_get_option('enable_jobs_filter_top');
$enable_jobs_show_map = felan_get_option('enable_jobs_show_map');
$jobs_map_postion = felan_get_option('jobs_map_postion');

if ($enable_jobs_show_map == 1 || $content_jobs == 'layout-full') {
	$jobs_filter_sidebar_option = 'filter-canvas';
} else {
	$jobs_filter_sidebar_option = felan_get_option('jobs_filter_sidebar_option');
}

$jobs_filter_sidebar_option = !empty($_GET['filter']) ? Felan_Helper::felan_clean(wp_unslash($_GET['filter'])) : $jobs_filter_sidebar_option;
$jobs_map_postion = !empty($_GET['map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['map'])) : $jobs_map_postion;
$enable_jobs_show_map = !empty($_GET['has_map']) ? Felan_Helper::felan_clean(wp_unslash($_GET['has_map'])) : $enable_jobs_show_map;

$key = isset($_GET['s']) ? felan_clean(wp_unslash($_GET['s'])) : '';

$archive_class = array();
$archive_class[] = 'content-jobs area-jobs area-archive';

$class_scrollbar = '';
if ($content_jobs == 'layout-list') {
	$class_inner[] = 'layout-list';
} else if ($content_jobs == 'layout-full') {
	$archive_class[] = 'column-1';
	$class_scrollbar = 'custom-scrollbar';
} else {
	$class_inner[] = 'layout-grid';
}

$tax_query = array();
$meta_query = array();
$args = array(
	'posts_per_page' => $items_amount,
	'post_type' => 'jobs',
	'ignore_sticky_posts' => 1,
	'tax_query' => $tax_query,
	's' => $key,
	'meta_key' => 'felan-jobs_featured',
	'orderby' => 'meta_value date',
	'order' => 'DESC',
);

$enable_jobs_show_expires = felan_get_option('enable_jobs_show_expires');
if ($enable_jobs_show_expires == 1) {
	$args['post_status']  = array('publish', 'expired');
} else {
	$args['post_status']  = 'publish';
}

$pagination_type = felan_get_option('jobs_pagination_type');
if ($pagination_type == 'loadpage') {
	$paged_load = isset($_GET['nagi-paged']) ? felan_clean(wp_unslash($_GET['nagi-paged'])) : '1';
	$args['paged'] = $paged_load;
}

$meta_query[] = array(
	'key' => FELAN_METABOX_PREFIX . 'enable_jobs_package_expires',
	'value' => 0,
	'compare' => '=='
);

$company_id = isset($_GET['company_id']) ? felan_clean(wp_unslash($_GET['company_id'])) : '';
if ($company_id) {
	$meta_query[] = array(
		'key' => FELAN_METABOX_PREFIX . 'jobs_select_company',
		'value' => $company_id,
		'compare' => '=='
	);
}

//Salary
$salary_rate = isset($_GET['salary-rate']) ? felan_clean(wp_unslash($_GET['salary-rate'])) : '';
$salary_min = isset($_GET['salary-min']) ? intval(felan_clean(wp_unslash($_GET['salary-min']))) : 0;
$salary_max = isset($_GET['salary-max']) ? intval(felan_clean(wp_unslash($_GET['salary-max']))) : 0;
if (!empty($salary_rate)) {
	if (empty($salary_min) && empty($salary_max)) {
		$meta_query[] = array(
			'relation' => 'AND',
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_salary_rate',
				'value' => $salary_rate,
				'compare' => '=',
			),
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_salary_show',
				'value' => 'agree',
				'compare' => '!=',
			),
		);
	}
	if (!empty($salary_min) && empty($salary_max)) {
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_salary_convert_min',
				'value' => $salary_min,
				'type' => 'NUMERIC',
				'compare' => '>=',
			),
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_price_convert_min',
				'value' => $salary_min,
				'type' => 'NUMERIC',
				'compare' => '>=',
			),
		);
	}

	if (!empty($salary_max) && empty($salary_min)) {
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_salary_convert_max',
				'value' => array(1, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_price_convert_max',
				'value' => array(1, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
		);
	}

	if (!empty($salary_max) && !empty($salary_min)) {
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_salary_convert_max',
				'value' => array($salary_min, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_price_convert_max',
				'value' => array($salary_min, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_salary_convert_min',
				'value' => array($salary_min, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_price_convert_min',
				'value' => array($salary_min, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
		);
	}
} else {
	if (!empty($salary_min) && empty($salary_max)) {
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_salary_minimum',
				'value' => $salary_min,
				'type' => 'NUMERIC',
				'compare' => '>=',
			),
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_minimum_price',
				'value' => $salary_min,
				'type' => 'NUMERIC',
				'compare' => '>=',
			),
		);
	}

	if (!empty($salary_max) && empty($salary_min)) {
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_salary_maximum',
				'value' => array(1, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_maximum_price',
				'value' => array(1, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
		);
	}

	if (!empty($salary_max) && !empty($salary_min)) {
		$meta_query[] = array(
			'relation' => 'OR',
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_salary_maximum',
				'value' => array($salary_min, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_maximum_price',
				'value' => array($salary_min, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_salary_minimum',
				'value' => array($salary_min, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
			array(
				'key' => FELAN_METABOX_PREFIX . 'jobs_minimum_price',
				'value' => array($salary_min, $salary_max),
				'type' => 'NUMERIC',
				'compare' => 'BETWEEN',
			),
		);
	}
}

//location country
$location_country = isset($_GET['jobs-country']) ? felan_clean(wp_unslash($_GET['jobs-country'])) : '';
if (!empty($location_country)) {
	$taxonomy_state = get_categories(
		array(
			'taxonomy' => 'felan_state',
			'hide_empty' => false,
			'parent' => 0,
			'meta_query' => array(
				array(
					'key' => 'felan-state-country',
					'value' => $location_country,
					'compare' => '=',
				)
			)
		)
	);

	if (!empty($taxonomy_state)) {
		$keys_state = array();
		foreach ($taxonomy_state as $terms_state) {
			$keys_state[] = $terms_state->term_id;
		}
		$taxonomy_city = get_categories(
			array(
				'taxonomy' => 'felan_location',
				'meta_query' => array(
					array(
						'key' => 'felan-location-state',
						'value' => $keys_state,
						'compare' => 'IN'
					)
				)
			)
		);
		$keys_city = array();
		foreach ($taxonomy_city as $terms_city) {
			$keys_city[] = $terms_city->term_id;
		}
	} else {
		$keys_city = '';
	}
	$tax_query[] = array(
		'taxonomy' => 'felan_location',
		'field' => 'term_id',
		'terms' => $keys_city
	);
}

//location state
$location_state = isset($_GET['jobs-state']) ? felan_clean(wp_unslash($_GET['jobs-state'])) : '';
if (!empty($location_state)) {
	$term_state = get_term_by('slug', $location_state, 'felan_state');
	$location_state_id = $term_state ? $term_state->term_id : '';
	$taxonomy_terms_state = get_categories(
		array(
			'taxonomy' => 'felan_location',
			'meta_query' => array(
				array(
					'key' => 'felan-location-state',
					'value' => $location_state_id,
					'compare' => '=',
				)
			)
		)
	);
	$key_state = array();
	foreach ($taxonomy_terms_state as $terms) {
		$key_state[] = $terms->term_id;
	}
	$tax_query[] = array(
		'taxonomy' => 'felan_location',
		'field' => 'term_id',
		'terms' => $key_state
	);
}


$args['meta_query'] = array(
	'relation' => 'AND',
	$meta_query
);

//Current term
$jobs_location = isset($_GET['jobs-location']) ? felan_clean(wp_unslash($_GET['jobs-location'])) : '';
if (!empty($jobs_location)) {
	$current_term = get_term_by('slug', $jobs_location, 'felan_location');
} else {
	$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
}

$current_term_name = '';
if (!empty($current_term)) {
	$current_term_name = $current_term->name;
} elseif (empty($current_term) && isset($_GET['jobs-location']) && $_GET['jobs-location'] != '') {
	$current_term_name = felan_clean(wp_unslash($_GET['jobs-location']));
}

if (!empty($current_term)) {
	$taxonomy_title = $current_term->name;
	$taxonomy_name = $current_term->taxonomy;

	if (!empty($taxonomy_name) && $taxonomy_name !== 'felan_state') {
		$terms_param = isset($_GET[$taxonomy_name]) ? felan_clean(wp_unslash($_GET[$taxonomy_name])) : $current_term->slug;
		$terms_array = is_string($terms_param) ? array_map('trim', explode(',', $terms_param)) : (array) $terms_param;

		$valid_terms = array();
		foreach ($terms_array as $term_slug) {
			$term_obj = get_term_by('slug', $term_slug, $taxonomy_name);
			if ($term_obj) {
				$valid_terms[] = $term_obj->slug;
			}
		}

		if (!empty($valid_terms)) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy_name,
				'field'    => 'slug',
				'terms'    => $valid_terms,
				'operator' => 'IN'
			);
		}
	}
} elseif (empty($current_term) && isset($_GET['jobs-location']) && $_GET['jobs-location'] != '') {
	$taxonomy_name = 'felan_location';
	$tax_query[] = array(
		'taxonomy' => $taxonomy_name,
		'field' => 'slug',
		'terms' => felan_clean(wp_unslash($_GET['jobs-location'])),
	);
}

$tax_count = count($tax_query);
if ($tax_count > 0) {
	$args['tax_query'] = array(
		'relation' => 'AND',
		$tax_query
	);
}

$args = apply_filters('felan/archive-jobs/layout-default/query/args', $args);

$data = new WP_Query($args);
$total_post = $data->found_posts;

$first_job_id = 0;

if ($enable_jobs_show_map == 1 && $enable_jobs_single_popup != 1) {
	$class_inner[] = 'has-map';
} else if ($content_jobs == 'layout-full') {
	$class_inner[] = 'layout-full';
} else {
	$class_inner[] = 'no-map';
}

if ($total_post <= 0) {
	$class_inner[] = 'only-left';
}
?>
<?php if ($enable_jobs_show_map == 1 && $jobs_map_postion == 'map-top' && $enable_jobs_single_popup != 1) { ?>
	<div class="col-right">
		<?php
		/**
		 * @Hook: felan_archive_map_filter
		 *
		 * @hooked archive_map_filter
		 */
		do_action('felan_archive_map_filter');
		?>
	</div>
<?php } ?>

<?php if ($enable_jobs_filter_top == 1) { ?>
	<?php do_action('felan_archive_jobs_top_filter', $current_term, $total_post); ?>
<?php } ?>

<div class="inner-content container <?php echo join(' ', $class_inner); ?>">
	<div class="col-left <?php echo $class_scrollbar; ?>">

		<?php if ($jobs_filter_sidebar_option !== 'filter-right') {
			do_action('felan_archive_jobs_sidebar_filter', $current_term, $total_post);
		} ?>

		<?php
		/**
		 * @Hook: felan_output_content_wrapper_start
		 *
		 * @hooked output_content_wrapper_start
		 */
		do_action('felan_output_content_wrapper_start');
		?>

		<div class="filter-warpper">
			<div class="entry-left">
				<div class="btn-canvas-filter <?php if ($jobs_filter_sidebar_option !== 'filter-canvas' && ($enable_jobs_show_map != 1 || $enable_jobs_single_popup == 1)) { ?>hidden-lg-up<?php } ?>">
					<a href="#"><i class="far fa-filter"></i><?php esc_html_e('Filter', 'felan-framework'); ?></a>
				</div>
				<span class="result-count">

					<?php if (!empty($key)) { ?>
						<?php
							printf(
								esc_html(_n('%1$s job for "%2$s"', '%1$s jobs for "%2$s"', $total_post, 'felan-framework')),
								'<span>' . $total_post . '</span>',
								$key
							);
						?>
					<?php } elseif (is_tax()) { ?>
						<?php
							printf(
								esc_html(_n('%1$s job for "%2$s"', '%1$s jobs for "%2$s"', $total_post, 'felan-framework')),
								'<span>' . $total_post . '</span>',
								$current_term_name
							);
						?>
					<?php } else { ?>
						<?php
							echo sprintf(
								_n( '%s job', '%s jobs', $total_post, 'felan-framework' ),
								'<span>' . $total_post . '</span>'
							);
						?>
					<?php } ?>
				</span>
			</div>
			<div class="entry-right">
				<div class="entry-filter">
					<div class="felan-clear-filter hidden-lg-up">
						<i class="far fa-sync"></i>
						<span><?php esc_html_e('Clear All', 'felan-framework'); ?></span>
					</div>
					<?php
					if ($content_jobs != 'layout-full') {
					?>
						<div class="jobs-layout switch-layout">
							<a class="<?php if ($content_jobs == 'layout-grid') : echo 'active';
										endif; ?>" href="#" data-layout="layout-grid"><i class="far far fa-th-large icon-large"></i></a>
							<a class="<?php if ($content_jobs == 'layout-list') : echo 'active';
										endif; ?>" href="#" data-layout="layout-list"><i class="far fa-list icon-large"></i></a>
						</div>
					<?php
					}
					?>
					<select name="sort_by" class="sort-by filter-control felan-select2">
						<option value="newest"><?php esc_html_e('Newest', 'felan-framework'); ?></option>
						<option value="oldest"><?php esc_html_e('Oldest', 'felan-framework'); ?></option>
						<option value="featured"><?php esc_html_e('Featured', 'felan-framework'); ?></option>
					</select>
					<?php if ($enable_jobs_show_map == 1 && $jobs_map_postion == 'map-right' && $enable_jobs_single_popup != 1) { ?>
						<div class="btn-control btn-switch btn-hide-map">
							<span class="text-switch"><?php esc_html_e('Map', 'felan-framework'); ?></span>
							<label class="switch">
								<input type="checkbox" value="hide_map">
								<span class="slider round"></span>
							</label>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="entry-mobie">
			<span class="result-count">
				<?php if (!empty($key)) { ?>
					<?php
						printf(
							esc_html(_n('%1$s job for "%2$s"', '%1$s jobs for "%2$s"', $total_post, 'felan-framework')),
							'<span>' . $total_post . '</span>',
							$key
						);
					?>
				<?php } elseif (is_tax()) { ?>
					<?php
						printf(
							esc_html(_n('%1$s job for "%2$s"', '%1$s jobs for "%2$s"', $total_post, 'felan-framework')),
							'<span>' . $total_post . '</span>',
							$current_term_name
						);
					?>
				<?php } else { ?>
					<?php
						echo sprintf(
							_n( '%s job', '%s jobs', $total_post, 'felan-framework' ),
							'<span>' . $total_post . '</span>'
						);
					?>
				<?php } ?>
			</span>
			<div class="felan-clear-filter hidden-lg-up">
				<i class="far fa-sync"></i>
				<span><?php esc_html_e('Clear All', 'felan-framework'); ?></span>
			</div>
		</div>

		<?php
		$company_id = isset($_GET['company_id']) ? felan_clean(wp_unslash($_GET['company_id'])) : '';
		?>
		<div class="<?php echo join(' ', $archive_class); ?>" data-company="<?= esc_attr($company_id) ?>">
			<?php
			$i = 1;
			if ($data->have_posts()) { ?>
				<?php while ($data->have_posts()) : $data->the_post(); ?>
					<?php
					if ($i == 1) {
						$first_job_id = get_the_ID();
					}
					felan_get_template('content-jobs.php', array(
						'jobs_layout' => $content_jobs,
					));
					?>
				<?php $i++;
				endwhile; ?>
			<?php } else { ?>
				<div class="item-not-found"><?php esc_html_e('No item found', 'felan-framework'); ?></div>
			<?php } ?>
		</div>

		<?php
		$max_num_pages = $data->max_num_pages;
		felan_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'type' => 'ajax-call', 'pagination_type' => $pagination_type));
		wp_reset_postdata();
		?>
		<?php
		/**
		 * @Hook: felan_output_content_wrapper_end
		 *
		 * @hooked output_content_wrapper_end
		 */
		do_action('felan_output_content_wrapper_end');
		?>

		<?php if ($jobs_filter_sidebar_option == 'filter-right' && ($enable_jobs_show_map != 1 || $enable_jobs_single_popup == 1)) {
			do_action('felan_archive_jobs_sidebar_filter', $current_term, $total_post);
		} ?>

	</div>
	<?php
	if ($enable_jobs_show_map == 1 && $jobs_map_postion == 'map-right' && $enable_jobs_single_popup != 1) {
		echo '<div class="col-right">';
		/**
		 * @Hook: felan_archive_map_filter
		 *
		 * @hooked archive_map_filter
		 */
		do_action('felan_archive_map_filter');
		echo '</div>';
	} elseif ($content_jobs == 'layout-full' && $total_post > 0) {
		echo '<div class="col-right preview-job-wrapper">';
		$post_id = $first_job_id;
		$company_id = get_post_meta($post_id, FELAN_METABOX_PREFIX . 'jobs_select_company');
		$company_id = !empty($company_id) ? $company_id[0] : '';
		$enable_social_twitter = felan_get_option('enable_social_twitter', '1');
		$enable_social_linkedin = felan_get_option('enable_social_linkedin', '1');
		$enable_social_facebook = felan_get_option('enable_social_facebook', '1');
		$enable_social_instagram = felan_get_option('enable_social_instagram', '1');
		if ($company_id !== '') {
			$company_logo   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_logo');
			$company_categories =  get_the_terms($company_id, 'company-categories');
			$company_founded =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_founded');
			$company_phone =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_phone');
			$company_email =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_email');
			$company_size =  get_the_terms($company_id,  'company-size');
			$company_website =  get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_website');
			$company_twitter   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_twitter');
			$company_facebook   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_facebook');
			$company_instagram   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_instagram');
			$company_linkedin   = get_post_meta($company_id, FELAN_METABOX_PREFIX . 'company_linkedin');
			$mycompany = get_post($company_id);
			$meta_query = felan_posts_company($company_id);
			$meta_query_post = felan_posts_company($company_id, 5);
			$company_location =  get_the_terms($company_id, 'felan_location');
		}
	?>
		<div id="jobs-<?php echo $post_id; ?>">
			<div class="block-jobs-warrper">
				<div class="block-archive-top">
					<?php
					/**
					 * Hook: felan_preview_jobs_before_summary hook.
					 */
					do_action('felan_preview_jobs_before_summary', $post_id); ?>
					<div class="preview-tabs">
						<div id="job-detail" class="tab-content is-active">
							<?php
							/**
							 * Hook: felan_preview_jobs_summary hook.
							 */
							do_action('felan_preview_jobs_summary', $post_id);
							?>
						</div>
					</div>
				</div>
				<?php
				/**
				 * Hook: felan_after_content_single_jobs_summary hook.
				 */
				do_action('felan_after_content_single_jobs_summary', $post_id);
				?>
				<?php
				/**
				 * Hook: felan_apply_single_jobs hook.
				 */
				do_action('felan_apply_single_jobs', $post_id);
				?>
			</div>
		</div>
	<?php
		echo '</div>';
	}
	?>
</div>