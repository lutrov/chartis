<?php

/*
Plugin Name: Chartis
Description: Ensure that search engines know about all the <em>pages</em>, <em>posts</em> and <em>custom post types</em> on your site with this simple, dynamic XML sitemap generator. Why this plugin name? Chartis means "map" in Greek.
Author: Ivan Lutrov
Author URI: http:// lutrov.com/
Version: 2.0
Notes: This plugin provides an API to customise the default constant values and control the post types to include in the sitemap. See the "readme.md" file for more.
*/

defined('ABSPATH') || die('Ahem.');

//
//  Dynamically generate XML sitemap.
//
function chartis_sitemap() {
	global $wpdb;
	$result = sprintf("%s<?xml version=\"1.0\" encoding=\"%s\"?>\n", null, get_bloginfo('charset'));
	$result = sprintf("%s<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n", $result);
	$result = sprintf("%s<url>\n", $result);
	$result = sprintf("%s<loc>%s</loc>\n", $result, home_url('/'));
	$result = sprintf("%s<lastmod>%s</lastmod>\n", $result, mysql2date('Y-m-d\TH:i:s+00:00', get_lastpostmodified('GMT'), false));
	$result = sprintf("%s<changefreq>daily</changefreq>\n", $result);
	$result = sprintf("%s<priority>1</priority>\n", $result);
	$result = sprintf("%s</url>\n", $result);
	$fp = (int) get_option('page_on_front');
	$query = sprintf("SELECT ID, post_type, post_title, post_modified_gmt FROM %s WHERE ID <> %s AND post_type IN (%s) AND post_status = 'publish' AND post_password = '' ORDER BY post_type ASC, post_name ASC", $wpdb->posts, $fp, chartis_post_types());
	$posts = $wpdb->get_results($query);
	foreach ($posts as $post) {
		if (strlen($post->post_title) > 0) {
			$result = sprintf("%s<url>\n", $result);
			$result = sprintf("%s<loc>%s</loc>\n", $result, get_permalink($post->ID));
			$result = sprintf("%s<lastmod>%s</lastmod>\n", $result, mysql2date('Y-m-d\TH:i:s+00:00', $post->post_modified_gmt, false));
			$result = sprintf("%s<changefreq>daily</changefreq>\n", $result);
			$result = sprintf("%s<priority>0.8</priority>\n", $result);
			$result = sprintf("%s</url>\n", $result);
		}
	}
	$result = sprintf("%s</urlset>", $result);
	header('HTTP/1.1 200 OK');
	header('X-Robots-Tag: noindex, follow', true);
	header('Content-Type: text/xml');
	echo sprintf("%s\n", $result);
	exit();
}

//
// Get post types to include.
//
function chartis_post_types() {
	$types = apply_filters('chartis_post_types_filter', array('page', 'post'));
	return sprintf("'%s'", implode("', '", $types));
}

//
// Update robots.txt file.
//
function chartis_robots_textfile($action) {
	$path = sprintf('%s/robots.txt', rtrim(ABSPATH, '/'));
	switch ($action) {
		case 'install':
			if (($fp = fopen($path, 'w'))) {
				fwrite($fp, sprintf('Sitemap: %s/sitemap.xml', site_url()));
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

//
// Invoke plugin if XML sitemap requested.
//
if (preg_match('#/sitemap\.xml$#', $_SERVER['REQUEST_URI']) == 1) {
	add_action('template_redirect', 'chartis_sitemap');
}

?>
