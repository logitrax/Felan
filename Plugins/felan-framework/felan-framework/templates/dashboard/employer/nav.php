<?php
$current_user = wp_get_current_user();
$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
$enable_wallet_mode = felan_get_option('enable_wallet_mode','0');

$key_employer = array(
    "dashboard" => esc_html__('Dashboard', 'felan-framework'),
    "company" => esc_html__('My Company', 'felan-framework'),
    "messages" => esc_html__('Messages', 'felan-framework'),
    "freelancers" => esc_html__('Follow', 'felan-framework'),
	"verification" => esc_html__('Identity Verification', 'felan-framework'),
    "settings" => esc_html__('Settings', 'felan-framework'),
    "logout" => esc_html__('Logout', 'felan-framework'),
);

if($enable_wallet_mode == '0'){
    $key_employer = array_merge(
        array_slice($key_employer, 0, 3, true),
        array("user_package" => esc_html__('My Package', 'felan-framework')),
        array_slice($key_employer, 3, null, true)
    );
} else {
    $key_employer = array_merge(
        array_slice($key_employer, 0, 3, true),
        array("wallet" => esc_html__('Wallet', 'felan-framework')),
        array_slice($key_employer, 3, null, true)
    );
}

if ($enable_post_type_jobs == '1') {
    $key_employer = array_merge(
        array_slice($key_employer, 0, 1, true),
        array("jobs_dashboard" => esc_html__('My Jobs', 'felan-framework')),
        array_slice($key_employer, 1, null, true)
    );

    $position = count($key_employer) - 3;
    $key_employer = array_merge(
        array_slice($key_employer, 0, $position, true),
        array("meetings" => esc_html__('Meetings', 'felan-framework')),
        array_slice($key_employer, $position, null, true)
    );
}

if ($enable_post_type_project == '1') {
    $key_employer = array_merge(
        array_slice($key_employer, 0, 1, true),
        array("projects" => esc_html__('My Projects', 'felan-framework')),
        array_slice($key_employer, 1, null, true)
    );
	$key_employer = array_merge(
        array_slice($key_employer, 0, 2, true),
        array("proposal" => esc_html__('Proposals', 'felan-framework')),
        array_slice($key_employer, 2, null, true)
    );
}

if ($enable_post_type_service == '1') {
    $key_employer = array_merge(
        array_slice($key_employer, 0, 3, true),
        array("service" => esc_html__('Bought Services', 'felan-framework')),
        array_slice($key_employer, 3, null, true)
    );
}


if($enable_post_type_project == '1' || $enable_post_type_service == '1'){
    $key_employer = array_merge(
        array_slice($key_employer, 0, 4, true),
        array("disputes" => esc_html__('Disputes', 'felan-framework')),
        array_slice($key_employer, 4, null, true)
    );
}

?>
<div class="nav-dashboard-inner">
	<div class="bg-overlay"></div>
	<div class="nav-dashboard-wapper custom-scrollbar">
		<div class="nav-dashboard nav-employer_dashboard">
			<div class="nav-dashboard-header">
				<div class="header-wrap">
					<?php echo Felan_Templates::site_logo('light'); ?>
				</div>
				<a href="#" class="closebtn">
					<i class="far fa-arrow-left"></i>
				</a>
			</div>
			<?php if (in_array('felan_user_employer', (array) $current_user->roles)) : ?>
				<ul class="list-nav-dashboard">
					<?php
					foreach ($key_employer as $key => $value) {
						if ($key ==  'service') {
							$key = 'employer_service';
						}
						$show_employer  = felan_get_option('show_employer_' . $key, '1');
						$image_employer = felan_get_option('image_employer_' . $key, '');
						$id             = felan_get_option('felan_' . $key . '_page_id');
					?>
						<?php if ($show_employer) : ?>
							<li class="nav-item <?php if (is_page($id) && $key !== "logout") : echo esc_attr('active');
												endif; ?>">
								<?php if ($key === "logout") { ?>
									<a href="<?php echo wp_logout_url(home_url()); ?>">
									<?php } else { ?>
										<a href="<?php echo get_permalink($id); ?>" class="felan-icon-items" data-title="<?php echo $value; ?>">
										<?php } ?>
										<?php if (!empty($image_employer['url'])) : ?>
											<span class="image">
												<?php if (felan_get_option('type_icon_employer') === 'svg') { ?>
													<object class="felan-svg" type="image/svg+xml" data="<?php echo esc_url($image_employer['url']) ?>"></object>
												<?php } else { ?>
													<img src="<?php echo esc_url($image_employer['url']) ?>" alt="<?php echo $value; ?>" />
												<?php } ?>
											</span>
										<?php endif; ?>
										<span><?php echo $value; ?></span>
										<?php if ($key === "messages") { ?>
											<?php felan_get_total_unread_message(); ?>
										<?php } ?>
										</a>
							</li>
						<?php endif; ?>
					<?php } ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
	<a href="#" class="icon-nav-mobie">
		<i class="far fa-bars"></i>
	</a>
</div>