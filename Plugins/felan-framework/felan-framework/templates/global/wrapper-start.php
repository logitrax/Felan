<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

$template = get_option('template');

switch ($template) {
	case 'twentyten':
		echo '<div id="container"><div id="content">';
		break;
	case 'twentyeleven':
		echo '<div id="primary"><div id="content" class="twentyeleven">';
		break;
	case 'twentytwelve':
		echo '<div id="primary" class="site-content"><div id="content" class="twentytwelve">';
		break;
	case 'twentythirteen':
		echo '<div id="primary" class="site-content"><div id="content" class="entry-content twentythirteen">';
		break;
	case 'twentyfourteen':
		echo '<div id="primary" class="content-area"><div id="content" class="site-content twentyfourteen"><div class="tfwc">';
		break;
	case 'twentyfifteen':
		echo '<div id="primary" class="content-area twentyfifteen"><div id="main" class="site-main t15wc">';
		break;
	case 'twentysixteen':
		echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main">';
		break;
	case 'twentyseventeen':
		echo '<div class="wrap"><div id="primary" class="content-area twentysixteen"><main id="main" class="site-main">';
		break;
	default:
		echo '<div id="primary"><main id="main" class="site-main">';
		break;
}
