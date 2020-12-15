<?php

/*
Plugin Name: Chartis
Description: Ensure that search engines know about all the <em>pages</em>, <em>posts</em> and <em>custom post types</em> on your site with this simple, dynamic XML sitemap generator. Why this plugin name? Chartis means "map" in Greek.
Plugin URI: https://github.com/lutrov/chartis
Author: Ivan Lutrov
Author URI: http:// lutrov.com/
Version: 4.0
Notes: This plugin provides an API to customise the default constant values and control the post types to include in the sitemap. See the "readme.md" file for more.
*/

defined('ABSPATH') || die('Ahem.');

//
// Invoke plugin if XML sitemap requested.
//
add_action('plugins_loaded', 'chartis_init_action', 1, 0);
function chartis_init_action() {
	if (preg_match('#/sitemap\.xml$#', $_SERVER['REQUEST_URI']) == 1) {
		add_action('template_redirect', 'chartis_sitemap_action', 1, 0);
		// Disable the intrinsic Wordpress sitemap
		add_filter('wp_sitemaps_enabled', '__return_false');
	}
}

//
//  Dynamically generate XML sitemap.
//
function chartis_sitemap_action() {
	$sitemap = array(home_url('/'));
	$args = array(
		'post_type' => apply_filters('chartis_post_types', array('page', 'post')),
		'post_status' => 'publish',
		'exclude' => (int) get_option('page_on_front'),
		'posts_per_page' => -1
	);
	$rows = get_posts($args);
	foreach ($rows as $row) {
		if (strlen($row->post_title) > 0) {
			array_push($sitemap, get_permalink($row->ID));
		}
	}
	if (count($sitemap) > 1) {
		sort($sitemap);
	}
	header('HTTP/1.1 200 OK');
	header('X-Robots-Tag: noindex, follow', true);
	header('Content-Type: text/xml');
	echo sprintf('<?xml version="1.0" encoding="%s"?>', get_bloginfo('charset'));
	echo sprintf('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
	foreach ($sitemap as $location) {
		echo sprintf('<url><loc>%s</loc></url>', $location);
	}
	echo sprintf('</urlset>');
	exit();
}

//
// Update robots.txt file.
//
function chartis_robots_textfile($action) {
	$path = sprintf('%s/robots.txt', rtrim(ABSPATH, '/'));
	switch ($action) {
		case 'install':
			if (($fp = fopen($path, 'w'))) {
				fwrite($fp, sprintf("User-Agent: *\nDisallow:\nSitemap: %s/sitemap.xml\n", site_url()));
				fclose($fp);
			}
			break;
		case 'uninstall':
			if (file_exists($path) == true) {
				unlink($path);
			}
			break;
	}
}

//
// Register plugin activation hook.
//
register_activation_hook(__FILE__, 'chartis_activate');
function chartis_activate() {
	chartis_robots_textfile('install');
}

//
// Register plugin deactivation hook.
//
register_deactivation_hook(__FILE__, 'chartis_deactivate');
function chartis_deactivate() {
	chartis_robots_textfile('uninstall');
}

?>
