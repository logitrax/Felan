<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$enable_post_type_jobs = felan_get_option('enable_post_type_jobs','1');
$enable_post_type_service = felan_get_option('enable_post_type_service','1');
$enable_post_type_project = felan_get_option('enable_post_type_project','1');

if($enable_post_type_jobs == '1' && $enable_post_type_service !== '1' && $enable_post_type_project !== '1'){
    felan_get_template('dashboard/freelancer/dashboard/jobs.php');
} else {
    felan_get_template('dashboard/freelancer/dashboard/freelance.php');
}