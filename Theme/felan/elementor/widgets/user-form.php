<?php

namespace Felan_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined('ABSPATH') || exit;

class Widget_User_Form extends Base
{

	public function get_name()
	{
		return 'felan-user-form';
	}

	public function get_title()
	{
		return esc_html__('Modern User Form', 'felan');
	}

	public function get_icon_part()
	{
		return 'eicon-form-horizontal';
	}

	public function get_keywords()
	{
		return ['form'];
	}

	public function get_script_depends()
	{
		return ['facebook-api', 'google-api', 'felan-widget-user-form'];
	}

	public function get_style_depends()
	{
		return ['felan-el-widget-user-form'];
	}

	protected function register_controls()
	{
		$this->add_user_form_section();
	}

	private function add_user_form_section()
	{
		$this->start_controls_section('user_form_section', [
			'label' => esc_html__('User Form', 'felan'),
		]);

		$this->add_control('form', [
			'label'   => esc_html__('Form', 'felan'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'login'   => 'Login',
				'register'   => 'Register',
				'login_register'   => 'Login & Register',
			],
			'default' => 'login',
		]);

		$this->add_control('role', [
			'label'   => esc_html__('Role', 'felan'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'freelancer'   => 'Freelancer',
				'employer'   => 'Employer',
				'freelancer_employer'   => 'Freelancer or Employer',
			],
			'default' => 'freelancer_employer',
			'condition'    => [
				'form!' => 'login',
			],
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		global $wp;
		$current_page_url = home_url($wp->request);
		$settings = $this->get_settings_for_display();
		$role = $settings['role'];
		$captcha = rand(1000, 9999);
		$_SESSION['felan_captcha'] = $captcha;
		if (is_user_logged_in()) {
?>
			<p class="notice success"><i class="fal fa-check-circle"></i><?php echo esc_html__('You are logged in!', 'felan'); ?></p>
		<?php
		} else {
		?>
			<div class="loading-effect"><span class="felan-dual-ring"></span></div>
			<?php
			if ($settings['form'] == 'login') {
			?>
				<form action="#" class="form-account active ux-login alway-show" method="post" data-redirect="yes">

					<?php do_action('felan_user_demo_sign_in'); ?>

					<div class="form-group">
						<label for="el_ip_email" class="label-field"><?php esc_html_e('Account or Email', 'felan'); ?></label>
						<input type="text" id="el_ip_email" class="form-control input-field" name="email" placeholder="<?php esc_attr_e('Enter Account or Email', 'felan') ?>">
					</div>
					<div class="form-group">
						<label for="el_ip_password" class="label-field"><?php esc_html_e('Password', 'felan'); ?></label>
						<input type="password" id="el_ip_password" class="form-control input-field" name="password" autocomplete="on" placeholder="<?php esc_attr_e('Enter Password', 'felan') ?>">
						<span toggle="#el_ip_password" class="fa fa-fw fa-eye field-icon felan-toggle-password"></span>
					</div>

                    <?php
                    $enable_captcha = \Felan_Helper::felan_get_option('enable_captcha');
                    if ($enable_captcha) :
                        ?>
                        <div class="form-group form-captcha">
                            <input type="text" class="form-control felan-captcha" name="ip_captcha" />
                            <input type="hidden" class="form-control felan-num-captcha" name="ip_num_captcha" data-captcha="<?php echo esc_attr($captcha); ?>" />
                            <?php
                            if (class_exists('Felan_Framework')) {
                                felan_image_captcha($captcha);
                            } ?>
                        </div>
                    <?php endif; ?>

					<div class="form-group">
						<div class="forgot-password">
							<span><?php esc_html_e('Forgot your password? ', 'felan'); ?></span>
							<a class="btn-reset-password" href="#"><?php esc_html_e('Reset password.', 'felan'); ?></a>
						</div>
					</div>

					<p class="msg"><?php esc_html_e('Sending login info,please wait...', 'felan'); ?></p>

					<div class="form-group">
						<input type="hidden" name="current_page" value="<?php echo esc_attr($current_page_url); ?>">
						<button type="submit" class="gl-button btn button" value="<?php esc_attr_e('Sign in', 'felan'); ?>"><?php esc_html_e('Sign in', 'felan'); ?></button>
					</div>
				</form>
				<div class="felan-reset-password-wrap form-account">
					<div id="felan_messages_reset_password" class="felan_messages message"></div>
					<form method="post" enctype="multipart/form-data">
						<div class="form-group control-username">
							<input name="user_login" id="el_user_login" class="form-control control-icon" placeholder="<?php esc_attr_e('Enter your username or email', 'felan'); ?>">
							<?php wp_nonce_field('felan_reset_password_ajax_nonce', 'el_felan_security_reset_password'); ?>
							<input type="hidden" name="action" id="el_reset_password_action" value="felan_reset_password_ajax">
							<input type="hidden" name="type" value="elementor">
							<p class="msg"><?php esc_html_e('Sending info,please wait...', 'felan'); ?></p>
							<button type="submit" class="felan_forgetpass btn gl-button"><?php esc_html_e('Get new password', 'felan'); ?></button>
						</div>
					</form>
					<a class="back-to-login" href="#"><i class="fas fa-arrow-left"></i><?php esc_html_e('Back to login', 'felan'); ?></a>
				</div>
				<div class="addon-login-wrap">
					<?php
					$enable_social_login = \Felan_Helper::felan_get_option('enable_social_login', '1');
                    $enable_social_facebook = \Felan_Helper::felan_get_option('enable_facebook_login', '1');
                    $enable_social_google = \Felan_Helper::felan_get_option('enable_google_login', '1');
                    $enable_social_linkedin = \Felan_Helper::felan_get_option('enable_linkedin_login', '1');
					if (class_exists('Felan_Framework') && $enable_social_login) {
					?>

						<div class="addon-login">
							<?php esc_html_e('Or Continue with', 'felan'); ?>
						</div>

						<ul>
                            <?php if ($enable_social_facebook) { ?>
                                <li><a class="facebook-login" href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <?php } ?>
                            <?php if ($enable_social_google) { ?>
                                <li><a class="google-login" href="#"><i class="fab fa-google"></i></a></li>
                            <?php } ?>
                            <?php if ($enable_social_linkedin) { ?>
                                <li><a class="linkedin-login" href="<?php echo esc_url(\Felan_LinkedIn::getAuthUrl()); ?>"><i class="fab fa-linkedin-in"></i></a></li>
                            <?php } ?>
						</ul>

					<?php } ?>
				</div>
			<?php
			} else if ($settings['form'] == 'register') {
			?>
				<form action="#" class="form-account active ux-register" method="post" data-redirect="yes">
					<?php
					$enable_user_role = \Felan_Helper::felan_get_option('enable_user_role', '1');
                    $enable_default_user_role = \Felan_Helper::felan_get_option('enable_default_user_role');
                    $enable_post_type_jobs = \Felan_Helper::felan_get_option('enable_post_type_jobs', '1');
                    $enable_post_type_service = \Felan_Helper::felan_get_option('enable_post_type_service', '1');
                    $enable_post_type_project = \Felan_Helper::felan_get_option('enable_post_type_project', '1');
                    if ($enable_user_role) {
					?>
						<div class="form-group">
							<div class="row">
								<div class="col-6">
									<div class="col-group">
										<label for="felan_user_freelancer1" class="label-field radio-field">
                                            <input type="radio" value="felan_user_freelancer" id="felan_user_freelancer1" name="account_type" <?php if ($enable_default_user_role == 'freelancer') echo 'checked'; ?>>
											<span><i class="fal fa-user"></i>
                                                <?php if($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1'){ ?>
                                                    <?php esc_html_e('Candidate', 'felan'); ?>
                                                <?php } else {?>
                                                    <?php esc_html_e('Freelancer', 'felan'); ?>
                                                <?php } ?>
                                            </span>
										</label>
									</div>
								</div>
								<div class="col-6">
									<div class="col-group">
										<label for="felan_user_employer1" class="label-field radio-field">
                                            <input type="radio" value="felan_user_employer" id="felan_user_employer1" name="account_type" <?php if ($enable_default_user_role == 'employer') echo 'checked'; ?>>
											<span><i class="fal fa-briefcase"></i><?php esc_html_e('Employer', 'felan'); ?></span>
										</label>
									</div>
								</div>
							</div>
						</div>
                    <?php } else { ?>
					    <?php if ($enable_default_user_role === 'employer' && $enable_user_role !== '1') { ?>
                            <input type="radio" checked value="felan_user_employer" id="felan_user_employer1" name="account_type" class="hide">
                        <?php } elseif($enable_default_user_role === 'freelancer' && $enable_user_role !== '1') { ?>
                            <input type="radio" checked value="felan_user_freelancer" id="felan_user_freelancer1" name="account_type" class="hide">
                        <?php } ?>
					<?php } ?>
					<div class="form-group">
						<div class="row">
							<div class="col-6">
								<div class="col-group">
									<label for="el_ip_reg_firstname" class="label-field"><?php esc_html_e('First Name', 'felan'); ?><span class="require">*</span></label>
									<input type="text" id="el_ip_reg_firstname" class="form-control input-field" name="reg_firstname" placeholder="<?php esc_attr_e('Name', 'felan') ?>">
								</div>
							</div>
							<div class="col-6">
								<div class="col-group">
									<label for="ip_reg_lastname" class="label-field"><?php esc_html_e('Last Name', 'felan'); ?><span class="require">*</span></label>
									<input type="text" id="ip_reg_lastname" class="form-control input-field" name="reg_lastname" placeholder="<?php esc_attr_e('Name', 'felan') ?>">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="ip_reg_company_name" class="label-field"><?php esc_html_e('Username', 'felan'); ?><span class="require">*</span></label>
						<input type="text" id="ip_reg_company_name" class="form-control input-field" name="reg_company_name" placeholder="<?php esc_attr_e('Enter Username', 'felan') ?>">
					</div>
					<div class="form-group">
						<label for="el_ip_reg_email" class="label-field"><?php esc_html_e('Email', 'felan'); ?><span class="require">*</span></label>
						<input type="email" id="el_ip_reg_email" class="form-control input-field" name="reg_email" placeholder="<?php esc_attr_e('Enter Email', 'felan') ?>">
					</div>
                    <div class="form-group">
                        <label for="ip_reg_phone" class="label-field"><?php esc_html_e('Phone number', 'felan') ?></label>
                        <div class="tel-group">
                            <select name="prefix_code" class="felan-select2 prefix-code">
                                <?php
                                $prefix_code = \Felan_Helper::phone_prefix_code();
                                $default_phone = \Felan_Helper::felan_get_option('default_phone_number');
                                foreach ($prefix_code as $key => $value) {
                                    $selected = '';
                                    if ($key == $default_phone) {
                                        $selected = 'selected';
                                    }
                                    echo '<option value="' . $key . '" data-dial-code="' . $value['code'] . '" ' . $selected . '>' . $value['name'] . ' (' . $value['code'] . ')</option>';
                                }
                                ?>
                            </select>
                            <?php
                            $default_phone_code = isset($prefix_code[$default_phone]) ? $prefix_code[$default_phone]['code'] : '';
                            ?>
                            <input type="tel" id="ip_reg_phone" name="reg_phone" value="<?php echo esc_attr($default_phone_code); ?>" placeholder="<?php esc_attr_e('Phone number', 'felan'); ?>">
                        </div>
                    </div>
					<div class="form-group">
						<label for="ip_reg_password" class="label-field"><?php esc_html_e('Password', 'felan'); ?><span class="require">*</span></label>
						<input type="password" id="ip_reg_password" class="form-control input-field" name="reg_password" autocomplete="on" placeholder="<?php esc_attr_e('Enter Password', 'felan') ?>">
					</div>

					<div class="form-group accept-account">
						<?php
						$terms_login 	= \Felan_Helper::felan_get_option('terms_condition');
						$privacy_policy = \Felan_Helper::felan_get_option('privacy_policy');
						?>
						<input type="checkbox" id="ip_accept_account1" class="form-control custom-checkbox" name="accept_account">
						<label for="ip_accept_account1"><?php printf(esc_html__('Accept the %1$s and %2$s', 'felan'), '<a href="' . get_permalink($terms_login) . '">' . esc_html__('Terms', 'felan') . '</a>', '<a href="' . get_permalink($privacy_policy) . '">' . esc_html__('Privacy Policy', 'felan') . '</a>'); ?></label>
					</div>

					<p class="msg"><?php esc_html_e('Sending register info,please wait...', 'felan'); ?></p>

					<div class="form-group">
						<button type="submit" class="gl-button btn button" value="<?php esc_attr_e('Sign in', 'felan'); ?>"><?php esc_html_e('Sign up', 'felan'); ?></button>
					</div>
				</form>

				<form action="#" id="ux-verify" class="form-account ux-verify" method="post">
					<?php if (\Felan_Helper::felan_get_option('enable_verify_user') === '1') : ?>
						<div class="form-group">
							<label for="verify-code" class="label-field"><?php esc_html_e('Verify Gmail', 'felan'); ?></label>
							<input type="text" id="verify-code" class="form-control input-field" name="verify_code" placeholder="<?php esc_attr_e('Enter Code', 'felan') ?>">
							<a href="#" class="resend" data-resend="gmail">
								<?php esc_html_e('Resend', 'felan'); ?>
								<span class="btn-loading"><i class="fal fa-spinner fa-spin medium"></i></span>
							</a>
						</div>
					<?php endif; ?>
					<p class="msg"><?php esc_html_e('Sending register info,please wait...', 'felan'); ?></p>
					<div class="form-group">
						<button type="submit" class="gl-button btn button" value="<?php esc_attr_e('Verify', 'felan'); ?>"><?php esc_html_e('Verify', 'felan'); ?></button>
					</div>
				</form>

                <div class="addon-login-wrap">
                    <?php
                    $enable_social_login = \Felan_Helper::felan_get_option('enable_social_login', '1');
                    $enable_social_facebook = \Felan_Helper::felan_get_option('enable_facebook_login', '1');
                    $enable_social_google = \Felan_Helper::felan_get_option('enable_google_login', '1');
                    $enable_social_linkedin = \Felan_Helper::felan_get_option('enable_linkedin_login', '1');
                    if (class_exists('Felan_Framework') && $enable_social_login) {
                        ?>

                        <div class="addon-login">
                            <?php esc_html_e('Or Continue with', 'felan'); ?>
                        </div>

                        <ul>
                            <?php if ($enable_social_facebook) { ?>
                                <li><a class="facebook-login" href="#"><i class="fab fa-facebook-f"></i></a></li>
                            <?php } ?>
                            <?php if ($enable_social_google) { ?>
                                <li><a class="google-login" href="#"><i class="fab fa-google"></i></a></li>
                            <?php } ?>
                            <?php if ($enable_social_linkedin) { ?>
                                <li><a class="linkedin-login" href="<?php echo esc_url(\Felan_LinkedIn::getAuthUrl()); ?>"><i class="fab fa-linkedin-in"></i></a></li>
                            <?php } ?>
                        </ul>

                    <?php } ?>
                </div>
			<?php
			} else {
			?>
				<div class="el-user-form">
					<div class="el-uf-nav">
						<a href="#el-ux-login" class="btn-login active" data-form="el-ux-login"><?php echo esc_html__('Sign In', 'felan'); ?></a>
						<a href="#el-ux-register" class="btn-register" data-form="el-ux-register"><?php echo esc_html__('Sign Up', 'felan'); ?></a>
					</div>
					<div class="el-uf-content">
						<div class="el-uf-item el-ux-login active">
							<form action="#" class="form-account active ux-login alway-show" method="post" data-redirect="yes">

								<?php do_action('felan_user_demo_sign_in'); ?>

								<div class="form-group">
									<label for="el_ip_email" class="label-field"><?php esc_html_e('Account or Email', 'felan'); ?></label>
									<input type="text" id="el_ip_email" class="form-control input-field" name="email" placeholder="<?php esc_attr_e('Enter Account or Email', 'felan') ?>">
								</div>
								<div class="form-group">
									<label for="el_ip_password" class="label-field"><?php esc_html_e('Password', 'felan'); ?></label>
									<input type="password" id="el_ip_password" class="form-control input-field" name="password" autocomplete="on" placeholder="<?php esc_attr_e('Enter Password', 'felan') ?>">
									<span toggle="#el_ip_password" class="fa fa-fw fa-eye field-icon felan-toggle-password"></span>
								</div>

                                <?php
                                $enable_captcha = \Felan_Helper::felan_get_option('enable_captcha');
                                if ($enable_captcha) :
                                    ?>
                                    <div class="form-group form-captcha">
                                        <input type="text" class="form-control felan-captcha" name="ip_captcha" />
                                        <input type="hidden" class="form-control felan-num-captcha" name="ip_num_captcha" data-captcha="<?php echo esc_attr($captcha); ?>" />
                                        <?php
                                        if (class_exists('Felan_Framework')) {
                                            felan_image_captcha($captcha);
                                        } ?>
                                    </div>
                                <?php endif; ?>

								<div class="form-group">
									<div class="forgot-password">
										<span><?php esc_html_e('Forgot your password? ', 'felan'); ?></span>
										<a class="btn-reset-password" href="#"><?php esc_html_e('Reset password.', 'felan'); ?></a>
									</div>
								</div>

								<p class="msg"><?php esc_html_e('Sending login info,please wait...', 'felan'); ?></p>

								<div class="form-group">
									<button type="submit" class="gl-button btn button" value="<?php esc_attr_e('Sign in', 'felan'); ?>"><?php esc_html_e('Sign in', 'felan'); ?></button>
								</div>
							</form>
							<div class="felan-reset-password-wrap form-account">
								<div id="felan_messages_reset_password" class="felan_messages message"></div>
								<form method="post" enctype="multipart/form-data">
									<div class="form-group control-username">
										<input name="user_login" id="el_user_login" class="form-control control-icon" placeholder="<?php esc_attr_e('Enter your username or email', 'felan'); ?>">
										<?php wp_nonce_field('felan_reset_password_ajax_nonce', 'el_felan_security_reset_password'); ?>
										<input type="hidden" name="action" id="el_reset_password_action" value="felan_reset_password_ajax">
										<input type="hidden" name="type" value="elementor">
										<p class="msg"><?php esc_html_e('Sending info,please wait...', 'felan'); ?></p>
										<button type="submit" class="felan_forgetpass btn gl-button"><?php esc_html_e('Get new password', 'felan'); ?></button>
									</div>
								</form>
								<a class="back-to-login" href="#"><i class="fas fa-arrow-left"></i><?php esc_html_e('Back to login', 'felan'); ?></a>
							</div>
							<div class="addon-login-wrap">
								<?php
								$enable_social_login = \Felan_Helper::felan_get_option('enable_social_login', '1');
                                $enable_social_facebook = \Felan_Helper::felan_get_option('enable_facebook_login', '1');
                                $enable_social_google = \Felan_Helper::felan_get_option('enable_google_login', '1');
                                $enable_social_linkedin = \Felan_Helper::felan_get_option('enable_linkedin_login', '1');
								if (class_exists('Felan_Framework') && $enable_social_login) {
								?>

									<div class="addon-login">
										<?php esc_html_e('Or Continue with', 'felan'); ?>
									</div>

									<ul>
                                        <?php if ($enable_social_facebook) { ?>
                                            <li><a class="facebook-login" href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <?php } ?>
                                        <?php if ($enable_social_google) { ?>
                                            <li><a class="google-login" href="#"><i class="fab fa-google"></i></a></li>
                                        <?php } ?>
                                        <?php if ($enable_social_linkedin) { ?>
                                            <li><a class="linkedin-login" href="<?php echo esc_url(\Felan_LinkedIn::getAuthUrl()); ?>"><i class="fab fa-linkedin-in"></i></a></li>
                                        <?php } ?>
									</ul>

								<?php } ?>
							</div>
						</div>
						<div class="el-uf-item el-ux-register">
							<form action="#" class="form-account active ux-register" method="post" data-redirect="yes">
								<?php
								$enable_user_role = \Felan_Helper::felan_get_option('enable_user_role', '1');
                                $enable_post_type_jobs = \Felan_Helper::felan_get_option('enable_post_type_jobs', '1');
                                $enable_post_type_service = \Felan_Helper::felan_get_option('enable_post_type_service', '1');
                                $enable_post_type_project = \Felan_Helper::felan_get_option('enable_post_type_project', '1');
								if ($enable_user_role && $role == 'freelancer_employer') {
								?>
									<div class="form-group">
										<div class="row">
											<div class="col-6">
												<div class="col-group">
													<label for="felan_user_freelancer1" class="label-field radio-field">
														<input type="radio" value="felan_user_freelancer" id="felan_user_freelancer1" name="account_type">
														<span><i class="fal fa-user"></i>
                                                            <?php if($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1'){ ?>
                                                                <?php esc_html_e('Candidate', 'felan'); ?>
                                                            <?php } else {?>
                                                                <?php esc_html_e('Freelancer', 'felan'); ?>
                                                            <?php } ?>
                                                        </span>
													</label>
												</div>
											</div>
											<div class="col-6">
												<div class="col-group">
													<label for="felan_user_employer1" class="label-field radio-field">
														<input type="radio" value="felan_user_employer" id="felan_user_employer1" name="account_type" checked>
														<span><i class="fal fa-briefcase"></i><?php esc_html_e('Employer', 'felan'); ?></span>
													</label>
												</div>
											</div>
										</div>
									</div>
								<?php } else if ($role == 'freelancer') { ?>
									<input type="radio" class="hide" value="felan_user_freelancer" id="felan_user_freelancer1" name="account_type" checked>
								<?php } else if ($role == 'employer') { ?>
									<input type="radio" class="hide" value="felan_user_employer" id="felan_user_employer1" name="account_type" checked>
								<?php } else { ?>
									<input type="radio" class="hide" value="felan_user_freelancer" id="felan_user_freelancer1" name="account_type" checked>
								<?php } ?>
								<div class="form-group">
									<div class="row">
										<div class="col-6">
											<div class="col-group">
												<label for="el_ip_reg_firstname" class="label-field"><?php esc_html_e('First Name', 'felan'); ?><span class="require">*</span></label>
												<input type="text" id="el_ip_reg_firstname" class="form-control input-field" name="reg_firstname" placeholder="<?php esc_attr_e('Name', 'felan') ?>">
											</div>
										</div>
										<div class="col-6">
											<div class="col-group">
												<label for="el_ip_reg_lastname" class="label-field"><?php esc_html_e('Last Name', 'felan'); ?><span class="require">*</span></label>
												<input type="text" id="el_ip_reg_lastname" class="form-control input-field" name="reg_lastname" placeholder="<?php esc_attr_e('Name', 'felan') ?>">
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="el_ip_reg_company_name" class="label-field"><?php esc_html_e('Username', 'felan'); ?><span class="require">*</span></label>
									<input type="text" id="el_ip_reg_company_name" class="form-control input-field" name="reg_company_name" placeholder="<?php esc_attr_e('Enter Username', 'felan') ?>">
								</div>
								<div class="form-group">
									<label for="el_ip_reg_email" class="label-field"><?php esc_html_e('Email', 'felan'); ?><span class="require">*</span></label>
									<input type="email" id="el_ip_reg_email" class="form-control input-field" name="reg_email" placeholder="<?php esc_attr_e('Enter Email', 'felan') ?>">
								</div>
								<div class="form-group">
									<label for="el_ip_reg_password" class="label-field"><?php esc_html_e('Password', 'felan'); ?><span class="require">*</span></label>
									<input type="password" id="el_ip_reg_password" class="form-control input-field" name="reg_password" autocomplete="on" placeholder="<?php esc_attr_e('Enter Password', 'felan') ?>">
								</div>

								<div class="form-group accept-account">
									<?php
									$terms_login 	= \Felan_Helper::felan_get_option('terms_condition');
									$privacy_policy = \Felan_Helper::felan_get_option('privacy_policy');
									?>
									<input type="checkbox" id="ip_accept_account1" class="form-control custom-checkbox" name="accept_account">
									<label for="ip_accept_account1"><?php printf(esc_html__('Accept the %1$s and %2$s', 'felan'), '<a href="' . get_permalink($terms_login) . '">' . esc_html__('Terms', 'felan') . '</a>', '<a href="' . get_permalink($privacy_policy) . '">' . esc_html__('Privacy Policy', 'felan') . '</a>'); ?></label>
								</div>

								<p class="msg"><?php esc_html_e('Sending register info,please wait...', 'felan'); ?></p>

								<div class="form-group">
									<button type="submit" class="gl-button btn button" value="<?php esc_attr_e('Sign in', 'felan'); ?>"><?php esc_html_e('Sign up', 'felan'); ?></button>
								</div>
							</form>

							<form action="#" id="ux-verify" class="form-account ux-verify" method="post">
								<?php if (\Felan_Helper::felan_get_option('enable_verify_user') === '1') : ?>
									<div class="form-group">
										<label for="verify-code" class="label-field"><?php esc_html_e('Verify Gmail', 'felan'); ?></label>
										<input type="text" id="verify-code" class="form-control input-field" name="verify_code" placeholder="<?php esc_attr_e('Enter Code', 'felan') ?>">
										<a href="#" class="resend" data-resend="gmail">
											<?php esc_html_e('Resend', 'felan'); ?>
											<span class="btn-loading"><i class="fal fa-spinner fa-spin medium"></i></span>
										</a>
									</div>
								<?php endif; ?>
								<p class="msg"><?php esc_html_e('Sending register info,please wait...', 'felan'); ?></p>
								<div class="form-group">
									<button type="submit" class="gl-button btn button" value="<?php esc_attr_e('Verify', 'felan'); ?>"><?php esc_html_e('Verify', 'felan'); ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>
			<?php
			}
			?>
<?php
		}
	}
}
