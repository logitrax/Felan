<?php
if (!defined("ABSPATH")) {
    exit(); // Exit if accessed directly
}

$enable_post_type_jobs = felan_get_option('enable_post_type_jobs', '1');
$enable_post_type_service = felan_get_option('enable_post_type_service', '1');
$enable_post_type_project = felan_get_option('enable_post_type_project', '1');
$enable_wallet_mode = felan_get_option('enable_wallet_mode','0');

$key_dashboard = apply_filters(
    'felan/dashboard/freelancer/nav',
    [
        "freelancer_dashboard" => esc_html__('Dashboard', 'felan-framework'),
        "freelancer_messages" => esc_html__('Messages', 'felan-framework'),
        "freelancer_company" => esc_html__('My Following', 'felan-framework'),
        "freelancer_reviews" => esc_html__('My Reviews', 'felan-framework'),
        "freelancer_wallet" => esc_html__('Wallet', 'felan-framework'),
        "freelancer_profile" => esc_html__('Profile', 'felan-framework'),
        "freelancer_verification" => esc_html__('Identity Verification', 'felan-framework'),
        "freelancer_settings" => esc_html__('Settings', 'felan-framework'),
        "freelancer_logout" => esc_html__('Logout', 'felan-framework'),
    ]
);

if($enable_wallet_mode == '0'){
    $key_dashboard = array_merge(
        array_slice($key_dashboard, 0, 3, true),
        array("freelancer_user_package" => esc_html__('My Package', 'felan-framework')),
        array_slice($key_dashboard, 3, null, true)
    );
}

if ($enable_post_type_jobs == '1') {
    $key_dashboard = array_merge(
        array_slice($key_dashboard, 0, 1, true),
        array("my_jobs" => esc_html__('Applied Jobs', 'felan-framework')),
        array_slice($key_dashboard, 1, null, true)
    );

    $position = count($key_dashboard) - 3;
    $key_dashboard = array_merge(
        array_slice($key_dashboard, 0, $position, true),
        array("freelancer_meetings" => esc_html__('Meetings', 'felan-framework')),
        array_slice($key_dashboard, $position, null, true)
    );
}

if ($enable_post_type_service == '1') {
    $key_dashboard = array_merge(
        array_slice($key_dashboard, 0, 1, true),
        array("freelancer_service" => esc_html__('My Services', 'felan-framework')),
        array_slice($key_dashboard, 1, null, true)
    );
}

if ($enable_post_type_project == '1') {
    $key_dashboard = array_merge(
        array_slice($key_dashboard, 0, 2, true),
        array("my_project" => esc_html__('Proposals', 'felan-framework')),
        array_slice($key_dashboard, 2, null, true)
    );
}

if($enable_post_type_project == '1' || $enable_post_type_service == '1'){
    $key_dashboard = array_merge(
        array_slice($key_dashboard, 0, 3, true),
        array("freelancer_disputes" => esc_html__('Disputes', 'felan-framework')),
        array_slice($key_dashboard, 3, null, true)
    );
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$freelancer_id = felan_get_post_id_freelancer();
$profile_strength_percent = get_post_meta(
    $freelancer_id,
    FELAN_METABOX_PREFIX . "freelancer_profile_strength",
    true
);
if (empty($profile_strength_percent)) {
    $profile_strength_percent = 0;
}
?>
<div class="nav-dashboard-inner">
    <div class="bg-overlay"></div>
    <div class="nav-dashboard-wapper custom-scrollbar">
        <div class="nav-dashboard nav-freelancer_dashboard">
            <div class="nav-dashboard-header">
                <div class="header-wrap">
                    <?php echo Felan_Templates::site_logo("light"); ?>
                </div>
                <a href="#" class="closebtn">
                    <i class="far fa-arrow-left"></i>
                </a>
            </div>

            <?php if (in_array("felan_user_freelancer", (array)$current_user->roles)) : ?>

                <ul class="list-nav-dashboard">

                    <?php foreach ($key_dashboard as $key => $value) :

                        $show_freelancer = felan_get_option("show_" . $key, "1");

                        if (!$show_freelancer) {
                            continue;
                        }

                        $id = felan_get_option("felan_" . $key . "_page_id");
                        $image_freelancer = felan_get_option("image_" . $key, "");
                        $type_freelancer = felan_get_option("type_" . $key);

                        $class_active =
                            is_page($id) && $key !== "freelancer_logout" ? "active" : "";

                        $link_url = "";
                        $link_url =
                            $key === "freelancer_logout"
                            ? wp_logout_url(home_url())
                            : get_permalink($id);

                        $html_icon = "";
                        if (!empty($image_freelancer["url"])) {
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
                        <li class="nav-item <?php esc_html_e($class_active); ?>">
                            <a href="<?php echo esc_url($link_url); ?>" data-title="<?php echo $value; ?>">
                                <?php if (!empty($image_freelancer["url"])) { ?>
                                    <span class="image">
                                        <?php echo $html_icon; ?>
                                    </span>
                                <?php } ?>
                                <span><?php echo $value; ?></span>
                                <?php if ($key === "freelancer_messages") { ?>
                                    <?php felan_get_total_unread_message(); ?>
                                <?php } ?>
                            </a>
                        </li>
                    <?php
                    endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <a href="#" class="icon-nav-mobie">
        <i class="far fa-bars"></i>
    </a>
</div>