<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
$cv_file = felan_get_option('felan-cv-type');
$cv_max_file_size = felan_get_option('felan_image_max_file_size', '1000kb');
$text = '<i class="far fa-arrow-from-bottom large"></i> ' . esc_attr(sprintf(esc_html__('Upload CV (%s)', 'felan-framework'), $cv_file));
$upload_nonce = wp_create_nonce('felan_thumbnail_allow_upload');
$url = FELAN_AJAX_URL .  '?action=felan_thumbnail_upload_ajax&nonce=' . esc_attr($upload_nonce);

wp_enqueue_script('plupload');
wp_enqueue_script('jquery-validate');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'upload-cv');
wp_localize_script(
	FELAN_PLUGIN_PREFIX . 'upload-cv',
	'felan_upload_cv_vars',
	array(
		'ajax_url'    => FELAN_AJAX_URL,
		'title'   => esc_html__('Valid file formats', 'felan-framework'),
		'cv_file' => $cv_file,
		'cv_max_file_size' => $cv_max_file_size,
		'upload_nonce' => $upload_nonce,
		'url' => $url,
		'text' => $text,
	)
);

global $current_user;
$user_id = $current_user->ID;
$freelancer_id =  $fileUrl = '';
if (in_array('felan_user_freelancer', (array)$current_user->roles)) {
	$args_freelancer = array(
		'post_type' => 'freelancer',
		'author' => $user_id,
	);
	$query = new WP_Query($args_freelancer);
	$freelancer_id = $query->post->ID;
}

$jobs_id = get_the_ID();
$jobs_select_apply = !empty(get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_apply')) ? get_post_meta($jobs_id, FELAN_METABOX_PREFIX . 'jobs_select_apply')[0] : '';
$freelancer_resume = !empty($freelancer_id) ? get_post_meta($freelancer_id, FELAN_METABOX_PREFIX . 'freelancer_resume_id_list', false) : '';
$freelancer_resume = !empty($freelancer_resume) ? $freelancer_resume[0] : '';
$fileName = basename(get_attached_file($freelancer_resume));
if (!empty(wp_get_attachment_url($freelancer_resume))) {
	$fileUrl = wp_get_attachment_url($freelancer_resume);
}
?>
<form action="#" method="post" class="form-popup form-popup-apply" id="felan_form_apply_jobs" enctype="multipart/form-data">
	<div class="bg-overlay"></div>
	<div class="apply-popup custom-scrollbar">
		<a href="#" class="btn-close"><i class="far fa-times"></i></a>
		<h5><?php esc_html_e('Apply for this job', 'felan-framework') ?></h5>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="apply_message"><?php esc_html_e('Message', 'felan-framework') ?><sup> *</sup></label>
				<textarea id="apply_message" name="apply_message" rows="4" cols="50"></textarea>
			</div>
			<div class="form-group col-md-12 felan-upload-cv">
				<div class="form-field">
					<div id="cv_errors_log" class="errors-log"></div>
					<div id="felan_cv_plupload_container" class="file-upload-block preview">
						<div class="felan_cv_file felan_add-cv">
							<p id="felan_drop_cv">
								<?php if (!empty($fileName)) { ?>
									<button type="button" id="felan_select_cv">
										<i class="far fa-arrow-from-bottom large"></i>
										<?php esc_html_e($fileName); ?>
									</button>
								<?php } else { ?>
									<button type="button" id="felan_select_cv">
										<i class="far fa-arrow-from-bottom large"></i>
										<?php echo esc_attr(sprintf(esc_html__('Upload CV (%s)', 'felan-framework'), $cv_file)); ?>
									</button>
								<?php } ?>
							</p>
						</div>
						<input type="hidden" class="cv_url form-control" name="jobs_cv_url" value="<?php echo esc_attr($fileUrl); ?>" id="cv_url">
						<input type="hidden" class="type_apply form-control" name="type_apply" value="<?php esc_html_e($jobs_select_apply); ?>" id="type_apply">
					</div>
				</div>
			</div>
		</div>
		<div class="message_error"></div>
		<div class="button-warpper">
			<a href="#" class="felan-button button-outline button-block button-cancel"><?php esc_html_e('Cancel', 'felan-framework'); ?></a>
			<button type="submit" class="felan-button button-block btn-submit-apply-jobs" id="btn-apply-jobs-<?php echo $jobs_id ?>" data-jobs_id="<?php echo $jobs_id ?>" data-freelancer_id="<?php echo $freelancer_id ?>">
				<?php esc_html_e('Apply Jobs', 'felan-framework'); ?>
				<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
			</button>
		</div>
	</div>
</form>