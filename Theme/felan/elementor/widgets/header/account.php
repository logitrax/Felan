<?php

namespace Felan_Elementor;

defined('ABSPATH') || exit;

class Widget_Account extends Base
{

	public function get_name()
	{
		return 'felan-account';
	}

	public function get_title()
	{
		return esc_html__('Account', 'felan');
	}

	public function get_icon_part()
	{
		return 'eicon-my-account';
	}

	public function get_keywords()
	{
		return ['modern', 'account'];
	}

	protected function register_controls()
	{
		$this->add_account_section();
	}

	private function add_account_section()
	{
		$this->start_controls_section('account_section', [
			'label' => esc_html__('Account', 'felan'),
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		if (is_user_logged_in()) {
			$accent_color = \Felan_Helper::get_setting('accent_color');
			$secondary_color = \Felan_Helper::get_setting('secondary_color');
			$currency_sign_default = \Felan_Helper::felan_get_option('currency_sign_default');
			$currency_position = \Felan_Helper::felan_get_option('currency_position');
            $enable_switch_account = \Felan_Helper::felan_get_option('enable_switch_account');
            $enable_post_type_jobs = \Felan_Helper::felan_get_option('enable_post_type_jobs', '1');
            $enable_post_type_service = \Felan_Helper::felan_get_option('enable_post_type_service', '1');
            $enable_post_type_project = \Felan_Helper::felan_get_option('enable_post_type_project', '1');
            $enable_post_type_jobs = \Felan_Helper::felan_get_option('enable_post_type_jobs', '1');
            $enable_wallet_mode = \Felan_Helper::felan_get_option('enable_wallet_mode','0');

            $current_user = wp_get_current_user();
			$user_name = $current_user->display_name;
			$user_id = $current_user->ID;
			$user_link = get_edit_user_link($current_user->ID);
			$avatar_url = get_avatar_url($current_user->ID);
			$author_avatar_image_url = get_the_author_meta(
				"author_avatar_image_url",
				$current_user->ID
			);
			$author_avatar_image_id = get_the_author_meta(
				"author_avatar_image_id",
				$current_user->ID
			);
			if (!empty($author_avatar_image_url)) {
				$avatar_url = $author_avatar_image_url;
			}
			$current_user = wp_get_current_user();

            $key_employer = [];
            if ($enable_switch_account == '1') {
                if($enable_post_type_jobs == '1'){
                    $key_employer["switch_freelancer"] = esc_html__('Switch Candidate', 'felan');
                } else {
                    $key_employer["switch_freelancer"] = esc_html__('Switch Freelancer', 'felan');
                }
            }

            $key_employer = array_merge($key_employer, [
                "dashboard" => esc_html__('Dashboard', 'felan'),
                "company" => esc_html__('My Company', 'felan'),
                "messages" => esc_html__('Messages', 'felan'),
                "freelancers" => esc_html__('Follow', 'felan'),
				"verification" => esc_html__('Identity Verification', 'felan-framework'),
                "settings" => esc_html__('Settings', 'felan'),
                "logout" => esc_html__('Logout', 'felan'),
            ]);

            if($enable_wallet_mode == '0'){
                $key_employer = array_merge(
                    array_slice($key_employer, 0, 4, true),
                    array("user_package" => esc_html__('My Package', 'felan')),
                    array_slice($key_employer, 4, null, true)
                );
            } else {
                $key_employer = array_merge(
                    array_slice($key_employer, 0, 3, true),
                    array("wallet" => esc_html__('Wallet', 'felan')),
                    array_slice($key_employer, 3, null, true)
                );
            }

            if ($enable_post_type_jobs == '1') {
                $key_employer = array_merge(
                    array_slice($key_employer, 0, 2, true),
                    array("jobs_dashboard" => esc_html__('My Jobs', 'felan')),
                    array_slice($key_employer, 2, null, true)
                );

                $position = count($key_employer) - 2;
                $key_employer = array_merge(
                    array_slice($key_employer, 0, $position, true),
                    array("meetings" => esc_html__('Meetings', 'felan')),
                    array_slice($key_employer, $position, null, true)
                );
            }

            if ($enable_post_type_project == '1') {
                $key_employer = array_merge(
                    array_slice($key_employer, 0, 2, true),
                    array("projects" => esc_html__('My Projects', 'felan')),
                    array_slice($key_employer, 2, null, true)
                );
            }

			if ($enable_post_type_project == '1') {
				$key_employer = array_merge(
					array_slice($key_employer, 0, 2, true),
					array("proposal" => esc_html__('Proposals', 'felan-framework')),
					array_slice($key_employer, 2, null, true)
				);
			}

            if ($enable_post_type_service == '1') {
                $key_employer = array_merge(
                    array_slice($key_employer, 0, 3, true),
                    array("service" => esc_html__('Bought Services', 'felan')),
                    array_slice($key_employer, 3, null, true)
                );
            }

            if($enable_post_type_project == '1' || $enable_post_type_service == '1'){
                $key_employer = array_merge(
                    array_slice($key_employer, 0, 4, true),
                    array("disputes" => esc_html__('Disputes', 'felan')),
                    array_slice($key_employer, 4, null, true)
                );
            }

            //Freelancer
            $key_freelancer = [];
            if ($enable_switch_account == '1') {
                $key_freelancer["switch_employer"] = esc_html__('Switch Employer', 'felan');
            }

            $key_freelancer = array_merge($key_freelancer, [
                "freelancer_dashboard" => esc_html__('Dashboard', 'felan'),
                "freelancer_messages" => esc_html__('Messages', 'felan'),
                "freelancer_company" => esc_html__('My Following', 'felan'),
                "freelancer_reviews" => esc_html__('My Reviews', 'felan'),
                "freelancer_wallet" => esc_html__('Wallet', 'felan'),
                "freelancer_profile" => esc_html__('Profile', 'felan'),
				"freelancer_verification" => esc_html__('Identity Verification', 'felan-framework'),
                "freelancer_settings" => esc_html__('Settings', 'felan'),
                "freelancer_logout" => esc_html__('Logout', 'felan'),
            ]);

            if($enable_wallet_mode == '0'){
                $key_freelancer = array_merge(
                    array_slice($key_freelancer, 0, 4, true),
                    array("freelancer_user_package" => esc_html__('My Package', 'felan')),
                    array_slice($key_freelancer, 4, null, true)
                );
            }

            if ($enable_post_type_jobs == '1') {
                $key_freelancer = array_merge(
                    array_slice($key_freelancer, 0, 2, true),
                    array("my_jobs" => esc_html__('Applied Jobs', 'felan')),
                    array_slice($key_freelancer, 2, null, true)
                );

                $position = count($key_freelancer) - 3;
                $key_freelancer = array_merge(
                    array_slice($key_freelancer, 0, $position, true),
                    array("freelancer_meetings" => esc_html__('Meetings', 'felan')),
                    array_slice($key_freelancer, $position, null, true)
                );
            }

            if ($enable_post_type_service == '1') {
                $key_freelancer = array_merge(
                    array_slice($key_freelancer, 0, 2, true),
                    array("freelancer_service" => esc_html__('My Services', 'felan')),
                    array_slice($key_freelancer, 2, null, true)
                );
            }

            if ($enable_post_type_project == '1') {
                $key_freelancer = array_merge(
                    array_slice($key_freelancer, 0, 3, true),
                    array("my_project" => esc_html__('Proposals', 'felan')),
                    array_slice($key_freelancer, 3, null, true)
                );
            }

            if($enable_post_type_project == '1' || $enable_post_type_service == '1'){
                $key_freelancer = array_merge(
                    array_slice($key_freelancer, 0, 4, true),
                    array("freelancer_disputes" => esc_html__('Disputes', 'felan')),
                    array_slice($key_freelancer, 4, null, true)
                );
            }

			$enable_user_name  = \Felan_Helper::felan_get_option('enable_user_name_after_login', 1);
            $felan_dashboard_freelancer = get_page_link(\Felan_Helper::felan_get_option('felan_freelancer_dashboard_page_id', 0));
            $felan_dashboard_employer = get_page_link(\Felan_Helper::felan_get_option('felan_dashboard_page_id', 0));
            $user_demo = get_the_author_meta('felan-user_demo', $user_id);
?>
            <div class="account logged-in">
                <?php if ($avatar_url) : ?>
                    <div class="user-show">
                        <a class="avatar" href="#">
                            <img src="<?php echo esc_url(
                                $avatar_url
                            ); ?>" title="<?php echo esc_attr(
                                $user_name
                            ); ?>" alt="<?php echo esc_attr($user_name); ?>">
                                <span class="user-name">
									<?php if ($enable_user_name) {
                                        echo esc_html($user_name);
                                    } ?>
                                    <?php if($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1'){ ?>
                                        <?php if (in_array("felan_user_employer", (array)$current_user->roles)) {
                                            echo '<span class="role">' . esc_html__('Employer', 'felan') . '</span>';
                                        } ?>
                                        <?php if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                                            echo '<span class="role">' . esc_html__('Candidate', 'felan') . '</span>';
                                        } ?>
                                    <?php } else {?>
                                        <?php if (in_array("felan_user_employer", (array)$current_user->roles)) {
                                            if($enable_wallet_mode == '1'){
                                                $total_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'employer_withdraw_total_price', true);
                                                if (empty($total_price)) {
                                                    $total_price = 0;
                                                }
                                                if ($currency_position == 'before') {
                                                    $total_price = $currency_sign_default . \Felan_Helper::felan_format_number($total_price);
                                                } else {
                                                    $total_price = \Felan_Helper::felan_format_number($total_price) . $currency_sign_default;
                                                }
                                                echo '<span class="role">' . esc_html__('Employer', 'felan') . '<span class="price">(' . $total_price . ')</span></span>';
                                            } else {
                                                echo '<span class="role">' . esc_html__('Employer', 'felan') . '</span></span>';
                                            }
                                        } ?>
                                        <?php if (in_array("felan_user_freelancer", (array)$current_user->roles)) {
                                            $total_price = get_user_meta($user_id, FELAN_METABOX_PREFIX . 'freelancer_withdraw_total_price', true);
                                            if (empty($total_price)) {
                                                $total_price = 0;
                                            }
                                            if ($currency_position == 'before') {
                                                $total_price = $currency_sign_default . \Felan_Helper::felan_format_number($total_price);
                                            } else {
                                                $total_price = \Felan_Helper::felan_format_number($total_price) . $currency_sign_default;
                                            }
                                            echo '<span class="role">' . esc_html__('Freelancer', 'felan') . '<span class="price">(' . $total_price . ')</span></span>';
                                        } ?>
                                    <?php } ?>
								</span>
                            <i class="far fa-chevron-down"></i>
                        </a>
                    </div>
                <?php endif; ?>
                <?php if (
                    in_array("felan_user_freelancer", (array)$current_user->roles) ||
                    in_array("felan_user_employer", (array)$current_user->roles)
                ) : ?>
                    <div class="user-control felan-nav-dashboard" data-secondary="<?php echo esc_attr($secondary_color); ?>" data-accent="<?php echo esc_attr($accent_color); ?>">
                        <div class="inner-control nav-dashboard">
                            <ul class="list-nav-dashboard">
                                <?php if (in_array("felan_user_employer", (array)$current_user->roles)) :
                                    foreach ($key_employer as $key => $value) {
                                        if ($key ===  'service') {
                                            $key = 'employer_service';
                                        }
                                        $show_employer = felan_get_option("show_employer_" . $key, "1");
                                        $image_employer = felan_get_option("image_employer_" . $key);
                                        $id = felan_get_option("felan_" . $key . "_page_id");
                                        ?>
                                        <?php if ($show_employer) : ?>
                                            <li class="nav-item nav-employer <?php if (is_page($id) && $key !== "logout" && $key !== "switch_freelancer") :
                                                echo esc_attr("active");
                                            endif; ?>">
                                                <?php if ($key === "logout") { ?>
                                                <a href="<?php echo wp_logout_url(home_url()); ?>">
                                                    <?php } elseif ($key === "switch_freelancer") { ?>
                                                    <?php if ($user_demo == 'yes') { ?>
                                                    <a href="#" class="btn-add-to-message" data-text="<?php echo esc_attr('This is a "Demo" account, so you can not change it', 'felan'); ?>">
                                                        <?php } else { ?>
                                                        <a href="#" class="felan-switch-account" data-new-role="felan_user_freelancer" data-redirect="<?php echo esc_attr($felan_dashboard_freelancer); ?>">
                                                            <?php } ?>
                                                            <?php } else { ?>
                                                            <a href="<?php echo get_permalink($id); ?>" class="felan-icon-items">
                                                                <?php } ?>
                                                                <?php if (!empty($image_employer["url"])) : ?>
                                                                    <span class="image">
																			<?php if (felan_get_option('type_icon_employer') === 'svg') { ?>
                                                                                <object class="felan-svg" type="image/svg+xml" data="<?php echo esc_url($image_employer['url']) ?>"></object>
                                                                            <?php } else { ?>
                                                                                <img src="<?php echo esc_url($image_employer['url']) ?>" alt="<?php echo esc_attr($value); ?>" />
                                                                            <?php } ?>
																		</span>
                                                                <?php endif; ?>
                                                                <?php if ($key === "switch_freelancer") : ?>
                                                                    <span class="image"><i class="far fa-spinner"></i></span>
                                                                <?php endif; ?>

                                                                <span><?php echo esc_html($value) ?></span>
                                                                <?php if ($key === "messages") { ?>
                                                                    <?php felan_get_total_unread_message(); ?>
                                                                <?php } ?>
                                                            </a>
                                            </li>
                                        <?php endif; ?>
                                        <?php
                                    } ?>
                                <?php
                                elseif (in_array("felan_user_freelancer", (array)$current_user->roles)) :

                                    foreach ($key_freelancer as $key => $value) :

                                        $show_freelancer = felan_get_option('show_' . $key, '1');

                                        if (!$show_freelancer) {
                                            continue;
                                        }

                                        $id = felan_get_option("felan_" . $key . "_page_id");
                                        $image_freelancer = felan_get_option("image_" . $key, "");

                                        $class_active = (is_page($id) && $key !== "freelancer_logout" && $key !== "switch_employer") ? 'active' : '';

                                        $link_url = '';
                                        $link_url = $key === "freelancer_logout" ? wp_logout_url(home_url()) : get_permalink($id);

                                        $html_icon = '';
                                        if (!empty($image_freelancer['url'])) {
                                            if (felan_get_option("type_icon_freelancer") === "svg") {
                                                $html_icon =
                                                    '<object class="felan-svg" type="image/svg+xml" data="' .
                                                    esc_url($image_freelancer["url"]) .
                                                    '"></object>';
                                            } else {
                                                $html_icon =
                                                    '<img src="' .
                                                    esc_url($image_freelancer["url"]) .
                                                    '" alt="' .
                                                    $value .
                                                    '"/>';
                                            }
                                        }
                                        ?>
                                        <li class="nav-item nav-freelancer <?php echo esc_html($class_active) ?>">
                                            <?php if ($key === "switch_employer") { ?>
                                            <?php if ($user_demo == 'yes') { ?>
                                            <a href="#" class="btn-add-to-message" data-text="<?php echo esc_attr('This is a "Demo" account, so you can not change it', 'felan'); ?>">
                                                <?php } else { ?>
                                                <a href="#" class="felan-switch-account" data-new-role="felan_user_employer" data-redirect="<?php echo esc_attr($felan_dashboard_employer); ?>">
                                                    <?php } ?>
                                                    <?php } else { ?>
                                                    <a href="<?php echo esc_url($link_url) ?>">
                                                        <?php } ?>

                                                        <?php if (!empty($image_freelancer["url"])) { ?>
                                                            <span class="image">
																	<?php echo wp_kses_post($html_icon); ?>
																</span>
                                                        <?php } ?>
                                                        <?php if ($key === "switch_employer") : ?>
                                                            <span class="image"><i class="far fa-spinner"></i></span>
                                                        <?php endif; ?>

                                                        <span><?php echo esc_html($value); ?></span>
                                                        <?php if ($key === "freelancer_messages") { ?>
                                                            <?php felan_get_total_unread_message(); ?>
                                                        <?php } ?>
                                                    </a>
                                        </li>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
		<?php
		} else {
			$sp_sign_in = \Felan_Helper::felan_get_option('sp_sign_in');
		?>
			<div class="account">
				<a href="<?php echo get_permalink($sp_sign_in) ?>" class="btn-login"><?php esc_html_e("Sign in", "felan"); ?></a>
			</div>
		<?php
		} ?>
<?php
	}
}
