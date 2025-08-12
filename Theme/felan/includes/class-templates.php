<?php

if (!defined("ABSPATH")) {
	exit();
}

if (!class_exists("Felan_Templates")) {
	/**
	 *  Class Felan_Templates
	 */
	class Felan_Templates
	{
		public static function site_logo($type = "")
		{
			$logo = "";
			$logo_retina = "";

			if ($type == "dark") {
				$logo_dark = Felan_Helper::get_setting("logo_dark");
				$logo_dark_retina = Felan_Helper::get_setting(
					"logo_dark_retina"
				);

				if ($logo_dark) {
					$logo = $logo_dark;
				}

				if ($logo_dark_retina) {
					$logo_retina = $logo_dark_retina;
				}
			}

			if ($type == "light") {
				$logo_light = Felan_Helper::get_setting("logo_light");
				$logo_light_retina = Felan_Helper::get_setting(
					"logo_light_retina"
				);

				if ($logo_light) {
					$logo = $logo_light;
				}

				if ($logo_light_retina) {
					$logo_retina = $logo_light_retina;
				}
			}

			$site_name = get_bloginfo("name", "display");

			ob_start();
?>
			<?php if (!empty($logo)) : ?>
				<div class="site-logo">
					<a href="<?php echo esc_url(home_url("/")); ?>" title="<?php echo esc_attr(
																				$site_name
																			); ?>"><img src="<?php echo esc_url(
																									$logo
																								); ?>" data-retina="<?php echo esc_attr(
																														$logo_retina
																													); ?>" alt="<?php echo esc_attr($site_name); ?>"></a>
				</div>
			<?php else : ?>
				<div class="site-logo">
					<?php $blog_info = get_bloginfo("name"); ?>
					<?php if (!empty($blog_info)) : ?>
						<h1 class="site-title"><a href="<?php echo esc_url(
															home_url("/")
														); ?>" title="<?php echo esc_attr(
																			$site_name
																		); ?>"><?php bloginfo("name"); ?></a></h1>
						<p><?php bloginfo("description"); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php return ob_get_clean();
		}


		public static function style_logo()
		{
			$id = get_the_ID();
			$header_style = '';
			if (!empty($id)) {
				$header_style = get_post_meta($id, 'felan-header_style', true);
			}
			if ($header_style == 'light') {
				$header_logo = Felan_Templates::site_logo('light');
			} else {
				$header_logo = Felan_Templates::site_logo('dark');
			}
			return $header_logo;
		}

		public static function search_form()
		{
			$show_search_form = Felan_Helper::get_setting("show_search_form");
			if (!$show_search_form) {
				return;
			}
		?>
			<div class="site-search">
				<form action="<?php echo esc_url(home_url("/")); ?>" method="get" class="site-search-form">
					<div class="search-icon">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" aria-hidden="true" viewBox="0 0 24 24" role="img">
							<path vector-effect="non-scaling-stroke" stroke="var(--icon-color, #001e00)" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M10.688 18.377a7.688 7.688 0 100-15.377 7.688 7.688 0 000 15.377zm5.428-2.261L21 21"></path>
						</svg>
					</div>
					<input type="text" class="search-input" name="s" autocomplete="off" placeholder="<?php echo esc_attr('Search', 'felan'); ?>">
					<input type="hidden" name="post_type" value="jobs">
					<div class="reset-search">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M15 9.00002L9 15M8.99997 9L14.9999 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
							<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</div>
					<div class="list-post-type">
						<div class="post-type active"><span><?php echo esc_html__("Jobs", "felan"); ?></span><i class="far fa-chevron-down"></i></div>
						<ul>
							<li>
								<a href="#" data-post-type="jobs" data-post-type-label="<?php echo esc_attr__("Jobs", "felan"); ?>" class="active">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M8.308 21H15.692C19.4025 21 20.067 19.551 20.2609 17.787L20.9531 10.587C21.2023 8.39098 20.5562 6.59998 16.615 6.59998H7.385C3.44378 6.59998 2.79768 8.39098 3.04689 10.587L3.73914 17.787C3.93297 19.551 4.59753 21 8.308 21Z" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M8.30811 6.6V5.88C8.30811 4.287 8.30811 3 11.2617 3H12.7385C15.6921 3 15.6921 4.287 15.6921 5.88V6.6" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M9.81164 13.3312C7.51024 13.0799 5.25161 12.2948 3.2334 11" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M14.2334 13.3312C16.5348 13.0799 18.7934 12.2948 20.8116 11" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
										<circle cx="12" cy="13.5" r="2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
									<?php echo esc_html__("Jobs", "felan"); ?>
								</a>
							</li>
							<li>
								<a href="#" data-post-type="company" data-post-type-label="<?php echo esc_attr__("Companies", "felan"); ?>">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M12 8.94059V18M17 8.94059V18M7 8.94059V18M12.4472 3.10627L20.2111 7.01386C21.155 7.48888 20.8192 8.92079 19.7639 8.92079H4.23607C3.18084 8.92079 2.84503 7.48889 3.78885 7.01386L11.5528 3.10627C11.8343 2.96458 12.1657 2.96458 12.4472 3.10627ZM19.5 21H4.50001C3.67158 21 3 20.3284 3 19.5C3 18.6716 3.67158 18 4.50001 18H19.5C20.3284 18 21 18.6716 21 19.5C21 20.3284 20.3284 21 19.5 21Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
									<?php echo esc_html__("Companies", "felan"); ?>
								</a>
							</li>
							<li>
								<a href="#" data-post-type="freelancer" data-post-type-label="<?php echo esc_attr__("Freelancers", "felan"); ?>">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M21 19.7499C21 17.66 19.3304 14.682 17 14.023M15 19.75C15 17.099 12.3137 13.75 9 13.75C5.68629 13.75 3 17.099 3 19.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
										<circle cx="9" cy="7.25" r="3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M15 10.25C16.6569 10.25 18 8.90685 18 7.25C18 5.59315 16.6569 4.25 15 4.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
									<?php echo esc_html__("Freelancers", "felan"); ?>
								</a>
							</li>
							<li>
								<a href="#" data-post-type="service" data-post-type-label="<?php echo esc_attr__("Services", "felan"); ?>">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M9.47796 3H7.25C6.00736 3 5 4.00736 5 5.25V18.75C5 19.9926 6.00736 21 7.25 21H16.25C17.4926 21 18.5 19.9926 18.5 18.75V12M9.47796 3C10.7206 3 11.75 4.00736 11.75 5.25V7.5C11.75 8.74264 12.7574 9.75 14 9.75H16.25C17.4926 9.75 18.5 10.7574 18.5 12M9.47796 3C13.1679 3 18.5 8.3597 18.5 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M9 16.5H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
										<path d="M9 13.5H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
									<?php echo esc_html__("Services", "felan"); ?>
								</a>
							</li>
							<li>
								<a href="#" data-post-type="project" data-post-type-label="<?php echo esc_attr__("Projects", "felan"); ?>">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M4.9999 20.25C4.9999 20.6642 5.33568 21 5.7499 21H16.4019C17.565 21 17.9999 20.6348 17.9999 19.4V17.9549M4.9999 20.25C4.9999 19.0074 6.00726 18 7.2499 18H17.4019C17.6281 18 17.8267 17.9862 17.9999 17.9549M4.9999 20.25V6.2002C4.9999 5.06408 4.92789 3.81097 6.09169 3.21799C6.51952 3 7.07999 3 8.20009 3H17.4001C18.6353 3 18.9999 3.43658 18.9999 4.6001V16.4001C18.9999 17.3948 18.7176 17.8251 17.9999 17.9549" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
									</svg>
									<?php echo esc_html__("Projects", "felan"); ?>
								</a>
							</li>
						</ul>
					</div>
				</form>
				<div class="search-result"></div>
			</div>
		<?php
		}

		public static function main_menu()
		{
			$show_main_menu = Felan_Helper::get_setting("show_main_menu");

			if (!$show_main_menu) {
				return;
			}

			ob_start();
		?>
			<div class="site-menu main-menu desktop-menu default-menu">
				<?php if (has_nav_menu('main_menu')) {
					if (class_exists('Felan_Walker_Nav_Menu')) {
						wp_nav_menu(array(
							'menu_class' => 'menu',
							'container' => '',
							'theme_location' => 'main_menu',
							'walker' => new Felan_Walker_Nav_Menu,
						));
					} else {
						wp_nav_menu(array(
							'menu_class' => 'menu',
							'container' => '',
							'theme_location' => 'main_menu',
						));
					}
				} ?>
			</div>
		<?php return ob_get_clean();
		}

		public static function site_menu()
		{
			if (!class_exists("Felan_Framework")) {
				return;
			}

			ob_start();
		?>
			<div class="site-menu desktop-menu default-menu">
				<?php if (has_nav_menu('primary')) {
					if (class_exists('Felan_Walker_Nav_Menu')) {
						wp_nav_menu(array(
							'menu_class' => 'menu',
							'container' => '',
							'theme_location' => 'primary',
							'walker' => new Felan_Walker_Nav_Menu,
						));
					} else {
						wp_nav_menu(array(
							'menu_class' => 'menu',
							'container' => '',
							'theme_location' => 'primary',
						));
					}
				} ?>
			</div>
		<?php return ob_get_clean();
		}

		public static function mobile_menu()
		{

			ob_start();
		?>
			<div class="bg-overlay"></div>

			<div class="site-menu area-menu mobile-menu default-menu">

				<div class="inner-menu custom-scrollbar">

					<a href="#" class="btn-close">
						<i class="fal fa-times"></i>
					</a>

					<?php if (!class_exists("Felan_Framework")) : ?>
						<?php echo self::site_logo("dark"); ?>
					<?php endif; ?>

					<?php if (class_exists("Felan_Framework")) : ?>
						<div class="top-mb-menu">
							<?php echo self::account(); ?>
						</div>
					<?php endif; ?>

					<?php wp_nav_menu(array(
						"menu_class" => "menu",
						"container" => "",
						"theme_location" => "mobile_menu",
					)); ?>

					<?php echo self::add_project(); ?>
				</div>
			</div>
		<?php return ob_get_clean();
		}

		public static function canvas_menu()
		{
			$show_canvas_menu = Felan_Helper::get_setting("show_canvas_menu");

			ob_start();
		?>
			<div class="mb-menu canvas-menu canvas-left <?php if (!$show_canvas_menu) {
															echo "d-hidden";
														} ?>">
				<a href="#" class="icon-menu">
					<i class="far fa-bars"></i>
				</a>

				<?php echo self::mobile_menu(); ?>
			</div>
		<?php return ob_get_clean();
		}

		public static function search_icon($search_type = "icon", $ajax = false)
		{
			$ajax_class = "";
			if ($ajax) {
				$ajax_class = "felan-ajax-search";
			}

			$show_search_icon = Felan_Helper::get_setting("show_search_icon");
			if (!$show_search_icon) {
				return;
			}

			ob_start();
		?>
			<div class="block-search search-<?php echo esc_attr($search_type); ?>
			<?php echo esc_attr($ajax_class); ?>">
				<div class="icon-search">
					<i class="far fa-search"></i>
				</div>
			</div>
			<?php return ob_get_clean();
		}

		public static function header_categories()
		{
			$cate_border_color = $show_cate = '';
			$id = get_the_ID();
			if (!empty($id)) {
				$show_cate       = get_post_meta($id, 'felan-show_header_cate', true);
				$cate_border_color  = get_post_meta($id, 'felan-cate_border_color', true);
			};


			$show_categories = Felan_Helper::get_setting("show_categories");
			$post_type_categories = Felan_Helper::get_setting("post_type_categories");
			if (!$show_categories && $show_cate !== '1') {
				return;
			}

			if ($post_type_categories == 'company') {
				$taxonomy = 'company-categories';
			} elseif ($post_type_categories == 'freelancer') {
				$taxonomy = 'freelancer_categories';
			} elseif ($post_type_categories == 'service') {
				$taxonomy = 'service-categories';
			} elseif ($post_type_categories == 'project') {
				$taxonomy = 'project-categories';
			} else {
				$taxonomy = 'jobs-categories';
			}

			function display_subcategories($parent_id, $taxonomy, $cate_border_color)
			{
				$subcategories = get_categories(array(
					'taxonomy' => $taxonomy,
					'hide_empty' => 0,
					'parent' => $parent_id,
				));

				if (!empty($subcategories)) {
					echo '<ul class="sub-categories" data-border-color="' . esc_attr($cate_border_color) . '">';
					foreach ($subcategories as $subcategory) {
						echo '<li>';
						echo '<a href="' . esc_url(get_category_link($subcategory->term_id)) . '">' . esc_html($subcategory->name) . '</a>';
						display_subcategories($subcategory->term_id, $taxonomy, $cate_border_color);

						echo '</li>';
					}
					echo '</ul>';
				}
			}

			$args = array(
				'taxonomy' => $taxonomy,
				'hide_empty' => 0,
				'number' => 6,
				'parent' => 0,
			);

			$categories = get_categories($args);

			ob_start();
			if (!empty($categories) && $show_cate != '0') {
				echo '<div class="d-none d-xl-block felan-list-categories">';
				echo '<div class="container">';
				echo '<ul class="list-categories" data-border-color="' . esc_attr($cate_border_color) . '">';

				foreach ($categories as $category) {
					echo '<li>';
					echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
					display_subcategories($category->term_id, $taxonomy, $cate_border_color);
					echo '</li>';
				}

				echo '</ul>';
				echo '</div>';
				echo '</div>';
			}
		}

		public static function post_categories()
		{
			ob_start();

			$count_posts = wp_count_posts();
			$category_id = "";
			$blog_sidebar = Felan_Helper::get_setting("blog_sidebar");
			$sidebar = !empty($_GET["sidebar"])
				? Felan_Helper::felan_clean(wp_unslash($_GET["sidebar"]))
				: $blog_sidebar;

			if (is_category()) {
				$cate = get_category(get_query_var("cat"));
				$category_id = $cate->cat_ID;
			}
			$categories = get_categories([
				"orderby" => "count",
				"order" => "DESC",
				"number" => 5,
				"parent" => 0,
				"hide_empty" => true,
				"hierarchical" => true,
			]);

			if ($categories) : ?>
				<div class="felan-categories">
					<ul class="list-categories">
						<li class="<?php if (!is_front_page() && is_home()) :
										echo esc_attr("active");
									endif; ?>">
							<a href="<?php echo get_post_type_archive_link("post"); ?>">
								<span class="entry-name"><?php esc_html_e("All", "felan"); ?></span>
							</a>
						</li>
						<?php foreach ($categories as $category) {
							$category_link = get_category_link($category->term_id); ?>
							<li class="<?php if ($category_id == $category->term_id) :
											echo esc_attr("active");
										endif; ?>">
								<a href="<?php echo esc_url($category_link); ?>">
									<span class="entry-name"><?php echo esc_html($category->name); ?></span>
								</a>
							</li>
						<?php
						} ?>
					</ul>
				</div>
			<?php endif;

			return ob_get_clean();
		}

		public static function account()
		{
			$show_login = Felan_Helper::get_setting("show_login");
			$show_register = Felan_Helper::get_setting("show_register");
			$sp_sign_in = Felan_Helper::felan_get_option('sp_sign_in');
			$sp_sign_up = Felan_Helper::felan_get_option('sp_sign_up');

			if (
				!class_exists("Felan_Framework") ||
				(!$show_login) && (!$show_register)
			) {
				return;
			}

			ob_start();
			?>
			<?php if (is_user_logged_in()) {

				$accent_color 	   	   = Felan_Helper::get_setting('accent_color');
				$secondary_color 	   = Felan_Helper::get_setting('secondary_color');
				$currency_sign_default = Felan_Helper::felan_get_option('currency_sign_default');
				$currency_position = Felan_Helper::felan_get_option('currency_position');
				$enable_switch_account = Felan_Helper::felan_get_option('enable_switch_account');
                $enable_post_type_jobs = Felan_Helper::felan_get_option('enable_post_type_jobs', '1');
                $enable_post_type_service = Felan_Helper::felan_get_option('enable_post_type_service', '1');
                $enable_post_type_project = Felan_Helper::felan_get_option('enable_post_type_project', '1');
                $enable_post_type_jobs = Felan_Helper::felan_get_option('enable_post_type_jobs', '1');
                $enable_wallet_mode = Felan_Helper::felan_get_option('enable_wallet_mode','0');

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

				$enable_user_name = Felan_Helper::felan_get_option('enable_user_name_after_login', 1);
				$felan_dashboard_freelancer = get_page_link(Felan_Helper::felan_get_option('felan_freelancer_dashboard_page_id', 0));
				$felan_dashboard_employer = get_page_link(Felan_Helper::felan_get_option('felan_dashboard_page_id', 0));
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
											$total_price = $currency_sign_default . Felan_Helper::felan_format_number($total_price);
										} else {
											$total_price = Felan_Helper::felan_format_number($total_price) . $currency_sign_default;
										}
										echo '<span class="role">' . esc_html__('Freelancer', 'felan') . '<span class="price">(' . $total_price . ')</span></span>';
									} ?>
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
			?>
				<div class="account">
					<?php if ($show_login) : ?>
						<a href="<?php echo get_permalink($sp_sign_in) ?>" class="btn-login">
							<?php esc_html_e("Sign In", "felan"); ?></a>
					<?php endif; ?>
					<?php if ($show_register) : ?>
						<a href="<?php echo get_permalink($sp_sign_up) ?>" class="btn-login">
							<?php esc_html_e("Sign Up", "felan"); ?></a>
					<?php endif; ?>
				</div>
			<?php
			} ?>
		<?php return ob_get_clean();
		}

		public static function notification()
		{
			$show_icon_noti = Felan_Helper::get_setting("show_icon_noti");
			if (!class_exists("Felan_Framework") || !$show_icon_noti || !is_user_logged_in()) {
				return;
			}

			ob_start();

			felan_get_template('dashboard/notification/notification.php');

			return ob_get_clean();
		}


		public static function add_project()
		{
			global $current_user;
			$user_id = $current_user->ID;

			$show_add_project_button = Felan_Helper::get_setting(
				"show_add_project_button"
			);

			if (!class_exists("Felan_Framework") || !$show_add_project_button) {
				return;
			}
			$add_project = $add_project_not = $update_profile = '#';
			if (Felan_Helper::felan_get_option("felan_add_project_page_id")) {
				$add_project = get_page_link(Felan_Helper::felan_get_option("felan_add_project_page_id"));
			}

			if (Felan_Helper::felan_get_option("felan_add_project_not_page_id")) {
				$add_project_not = get_page_link(Felan_Helper::felan_get_option("felan_add_project_not_page_id"));
			}

			if (Felan_Helper::felan_get_option("felan_update_profile_page_id")) {
				$update_profile = get_page_link(Felan_Helper::felan_get_option("felan_update_profile_page_id"));
			}

			if (Felan_Helper::felan_get_option("felan_package_page_id")) {
				$package = get_page_link(Felan_Helper::felan_get_option("felan_package_page_id"));
			}

			$enable_login_to_submit = Felan_Helper::felan_get_option(
				"enable_login_to_submit",
				"1"
			);

			$paid_submission_type = Felan_Helper::felan_get_option('paid_submission_type');
			$package_number_job = get_the_author_meta(FELAN_METABOX_PREFIX . 'package_number_job', $user_id);
			$has_package = true;
			if ($paid_submission_type == 'per_package') {
				$felan_profile = new Felan_Profile();
				$check_package = $felan_profile->user_package_available($user_id);
				if (($check_package == -1) || ($check_package == 0)) {
					$has_package = false;
				}
			}
			ob_start();
		?>
			<?php if ($enable_login_to_submit == "1" && !is_user_logged_in()) { ?>
				<a href="<?php echo esc_url($add_project_not); ?>" class="felan-button add-job">
					<?php esc_html_e("Post Project", "felan"); ?>
				</a>
			<?php } else { ?>
				<?php if (in_array('felan_user_freelancer', (array)$current_user->roles)) { ?>
					<a href="<?php echo esc_url($update_profile); ?>" class="add-job felan-button">
						<?php esc_html_e("Update Profile", "felan"); ?>
					</a>
				<?php } else { ?>
					<?php if (($has_package && $package_number_job > 0) || $paid_submission_type !== 'per_package') { ?>
						<a href="<?php echo esc_url($add_project); ?>" class="add-job felan-button">
							<?php esc_html_e("Post Project", "felan"); ?>
						</a>
					<?php } else { ?>
						<a href="<?php echo esc_url($package); ?>" class="add-job felan-button">
							<?php esc_html_e("Post Project    ", "felan"); ?>
						</a>
					<?php } ?>


				<?php } ?>
			<?php } ?>
		<?php return ob_get_clean();
		}

		public static function render_button()
		{
			$show_button = Felan_Helper::get_setting("show_button");
			$button_text = Felan_Helper::get_setting("button_text");
			$button_link = Felan_Helper::get_setting("button_link");
			$button_background_color = Felan_Helper::get_setting("button_background_color");
			$button_text_color = Felan_Helper::get_setting("button_text_color");

			if (!class_exists("Felan_Framework") || !$show_button || !$button_text || !$button_link) {
				return;
			}

			ob_start();
		?>
			<a href="<?php echo esc_url($button_link); ?>" class="felan-button" style="background-color:<?php echo esc_attr($button_background_color); ?>; border-color:<?php echo esc_attr($button_background_color); ?>; color:<?php echo esc_attr($button_text_color); ?>;">
				<?php echo wp_kses_post($button_text); ?>
			</a>
		<?php
			return ob_get_clean();
		}

		//Top Bar
		public static function top_bar()
		{
			$top_bar_text = Felan_Helper::get_setting("top_bar_text");
			$top_bar_link = Felan_Helper::get_setting("top_bar_link");
			$top_bar_phone = Felan_Helper::get_setting("top_bar_phone");
			$top_bar_email = Felan_Helper::get_setting("top_bar_email");
			$top_bar_ringbell = Felan_Helper::get_setting('top_bar_ringbell');
			ob_start(); ?>
			<div class="col-lg-7 left-top-bar">
				<div class="top-bar-text">
					<a href="<?php echo esc_url($top_bar_link); ?>">
						<span class="icon-ringbell">
							<img src="<?php echo esc_url($top_bar_ringbell); ?>" alt="" />
						</span>
						<?php echo wp_kses_post($top_bar_text) ?>
					</a>
				</div>
			</div>
			<div class="col-lg-5 right-top-bar">
				<span class="top-bar-phone"><i class="fal fa-phone"></i><?php echo esc_html($top_bar_phone); ?></span>
				<span class="top-bar-email"><i class="fal fa-envelope"></i><?php echo esc_html($top_bar_email); ?></span>
			</div>
			<?php return ob_get_clean();
		}


		public static function page_title()
		{
			ob_start();

			get_template_part("templates/page/page-title");

			return ob_get_clean();
		}

		public static function post_thumbnail()
		{
			ob_start();

			get_template_part("templates/post/post-thumbnail");

			return ob_get_clean();
		}

		/**
		 * Render comments
		 * *******************************************************
		 */
		public static function render_comments($comment, $args, $depth)
		{
			self::felan_get_template("post/comment", [
				"comment" => $comment,
				"args" => $args,
				"depth" => $depth,
			]);
		}

		/**
		 * Get template
		 * *******************************************************
		 */
		public static function felan_get_template($slug, $args = [])
		{
			if ($args && is_array($args)) {
				extract($args);
			}
			$located = locate_template(["templates/{$slug}.php"]);

			if (!file_exists($located)) {
				_doing_it_wrong(
					__FUNCTION__,
					sprintf("<code>%s</code> does not exist.", $slug),
					"1.0"
				);
				return;
			}
			include $located;
		}

		/**
		 * Display navigation to next/previous set of posts when applicable.
		 */
		public static function pagination()
		{
			global $wp_query, $wp_rewrite;

			// Don't print empty markup if there's only one page.
			if ($wp_query->max_num_pages < 2) {
				return;
			}

			$paged = get_query_var("paged")
				? intval(get_query_var("paged"))
				: 1;
			$pagenum_link = wp_kses(
				get_pagenum_link(),
				Felan_Helper::felan_kses_allowed_html()
			);
			$query_args = [];
			$url_parts = explode("?", $pagenum_link);

			if (isset($url_parts[1])) {
				wp_parse_str($url_parts[1], $query_args);
			}

			$pagenum_link = esc_url(
				remove_query_arg(array_keys($query_args), $pagenum_link)
			);
			$pagenum_link = trailingslashit($pagenum_link) . "%_%";

			$format =
				$wp_rewrite->using_index_permalinks() &&
				!strpos($pagenum_link, "index.php")
				? "index.php/"
				: "";
			$format .= $wp_rewrite->using_permalinks()
				? user_trailingslashit(
					$wp_rewrite->pagination_base . "/%#%",
					"paged"
				)
				: "?paged=%#%";

			// Set up paginated links.
			$links = paginate_links([
				"format" => $format,
				"total" => $wp_query->max_num_pages,
				"current" => $paged,
				"add_args" => array_map("urlencode", $query_args),
				"prev_text" => '<i class="far fa-angle-left"></i>',
				"next_text" => '<i class="far fa-angle-right"></i>',
				"type" => "list",
				"end_size" => 3,
				"mid_size" => 3,
			]);

			if ($links) { ?>

				<div class="posts-pagination">
					<?php echo wp_kses($links, Felan_Helper::felan_kses_allowed_html()); ?>
				</div><!-- .pagination -->

<?php }
		}
	}
}
