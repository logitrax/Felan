<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script('plupload');
wp_enqueue_script('jquery-validate');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer-submit');
wp_enqueue_script(FELAN_PLUGIN_PREFIX . 'freelancer');
$custom_field_freelancer = felan_render_custom_field('freelancer');
wp_localize_script(
    FELAN_PLUGIN_PREFIX . 'freelancer-submit',
    'felan_freelancer_vars',
    array(
        'ajax_url' => FELAN_AJAX_URL,
        'site_url' => get_site_url(),
        'text_present' => esc_attr__('Present', 'felan-framework'),
        'custom_field_freelancer' => $custom_field_freelancer,
    )
);

felan_get_avatar_enqueue();
felan_get_thumbnail_enqueue();

global $current_user, $hide_freelancer_fields, $hide_freelancer_group_fields, $freelancer_data, $freelancer_meta_data;
wp_get_current_user();
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(FELAN_METABOX_PREFIX . 'user_demo', $user_id);

$ajax_url = admin_url('admin-ajax.php');
$upload_nonce = wp_create_nonce('freelancer_allow_upload');

$front_image_url = get_the_author_meta('front_image_url', $user_id);
$back_image_id = get_the_author_meta('back_image_id', $user_id);
$back_image_url = get_the_author_meta('back_image_url', $user_id);

$user_verify = get_the_author_meta('user_verify', $user_id);

?>

<div id="freelancer-profile" class="freelancer-profile">

    <div class="entry-my-page freelancer-profile-dashboard">

        <div class="entry-title">
            <h4><?php esc_html_e('Verify Your Identity', 'felan-framework') ?></h4>
        </div>

		<?php if ( $user_verify == 1 ) : ?>
			<div class="verify-status">
				<h5>
					<img style="" class="image-freelancers-verify" src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/check-badge-verified.png'); ?>" alt="" />
					<?php esc_html_e('Hurray!', 'felan-framework') ?>
				</h5>
				<p><?php esc_html_e('You are all set! Your Identity was successfully verified and your account is now fully active', 'felan-framework') ?></p>
			</div>

		<?php elseif ( !empty( $front_image_url ) && !empty( $back_image_url ) ) : ?>
			<div class="verify-status">
				<h5>
					<img style="" class="image-freelancers-verify" src="<?php echo esc_attr(FELAN_PLUGIN_URL . 'assets/images/check-badge-not-verify.png'); ?>" alt="" />
					<?php esc_html_e('Thank you for submitting', 'felan-framework') ?>
				</h5>
				<p><?php esc_html_e('Your documents will be reviewed. You will receive a notification once your Identity is confirmed.', 'felan-framework') ?></p>
			</div>
		<?php else : ?>

		<form action="#" method="post" enctype="multipart/form-data" id="freelancer-verify-form"  class="col-lg-8 col-md-7">
			<input type="hidden" name="user_id" value="<?php echo esc_attr($user_id) ?>">
			<!-- <input type="hidden" name="freelancer_profile_strength" value="<?php esc_attr_e($profile_strength_percent) ?>"> -->

			<div class="felan-verify-type">
				<label><?php esc_html_e( 'Select a Document to Upload', 'felan-framework' ); ?></label>
				<select name="verify-type" id="verify-type">
					<option value="National ID">National ID</option>
					<option value="Passport">Passport</option>
					<option value="Driver's License">Driver's License</option>
				</select>
			</div>
			<div class="felan-image-freelancer">
				<div class="freelancer-fields-avatar felan-fields-avatar">
					<label><?php esc_html_e('Front side', 'felan-framework'); ?></label>
					<div class="form-field">
						<div id="felan_avatar_errors" class="errors-log"></div>
						<div id="felan_avatar_container" class="file-upload-block preview">
							<div id="felan_avatar_view"
								data-image-id="<?php echo $user_id; ?>"
								data-image-url="<?php if (!empty($front_image_url)) {
									echo $front_image_url;
								} ?>">
							</div>
							<div id="felan_add_avatar">
								<i class="far fa-arrow-from-bottom large"></i>
								<p id="felan_drop_avatar">
									<button type="button" id="felan_select_avatar"><?php esc_html_e('Upload', 'felan-framework') ?></button>
								</p>
							</div>
							<input type="hidden"
								class="avatar_url form-control"
								name="author_avatar_image_url"
								value="<?php echo $front_image_url; ?>"
								id="avatar_url"
							>
							<input type="hidden"
								class="avatar_id"
								name="author_avatar_image_id"
								value="<?php echo $user_id; ?>"
								id="avatar_id"
							/>
						</div>
					</div>
				</div>
				<div class="freelancer-fields-thumbnail felan-fields-thumbnail">
					<label><?php esc_html_e('Back side', 'felan-framework'); ?></label>
					<div class="form-field">
						<div id="felan_avatar_errors" class="errors-log"></div>
						<div id="felan_thumbnail_container" class="file-upload-block preview">
							<div id="felan_thumbnail_view"
								data-image-id="<?php echo $back_image_id; ?>"
								data-image-url="<?php if (!empty($back_image_url)) {
									echo $back_image_url;
									} ?>">
							</div>
							<div id="felan_add_thumbnail">
								<i class="far fa-arrow-from-bottom large"></i>
								<p id="felan_drop_thumbnail">
									<button type="button" id="felan_select_thumbnail"><?php esc_html_e('Upload', 'felan-framework') ?></button>
								</p>
							</div>
							<input type="hidden"
								class="thumbnail_url form-control"
								name="freelancer_cover_image_url"
								value="<?php echo $back_image_url; ?>"
								id="thumbnail_url"
							>
							<input type="hidden"
								class="thumbnail_id"
								name="freelancer_cover_image_id"
								value="<?php echo $back_image_id; ?>"
								id="thumbnail_id"
							/>
						</div>
					</div>
				</div>
			</div>

			<div class="button-warpper">
				<button type="submit" class="btn-update-profile felan-button" name="submit_profile">
					<span><?php esc_html_e('Submit', 'felan-framework'); ?></span>
					<span class="btn-loading"><i class="far fa-spinner fa-spin large"></i></span>
				</button>
			</div>
		</form>

		<?php endif; ?>

    </div>

</div>