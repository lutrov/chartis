<?php

/*
Plugin Name: Chartis
Description: Ensure that search engines know about all the <em>pages</em>, <em>posts</em> and <em>products</em> on your site with this simple, dynamic XML sitemap generator. Please note that this plugin doesn't create a robots.txt file. You should be adding your XML sitemap URL via Google and Bing Webmaster Tools instead. Why this plugin name? Chartis means "map" in Greek.
Author: Ivan Lutrov
Version: 1.3
Author URI: http:// lutrov.com/
*/

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
	$sql = sprintf("SELECT ID, post_type, post_title, post_modified_gmt FROM %s WHERE ID <> %s AND post_type IN('page', 'post', 'product') AND post_status = 'publish' AND post_password = '' ORDER BY post_type ASC, post_modified DESC", $wpdb->posts, $fp);
	$posts = $wpdb->get_results($sql);
	foreach ($posts as $post) {
		if (strlen($post->post_title) > 0) {
			$result = sprintf("%s<url>\n", $result);
			$result = sprintf("%s<loc>%s</loc>\n", $result, get_permalink($post->ID));
			$result = sprintf("%s<lastmod>%s</lastmod>\n", $result, mysql2date('Y-m-d\TH:i:s+00:00', $post->post_modified_gmt, false));
			$result = sprintf("%s<changefreq>weekly</changefreq>\n", $result);
			$result = sprintf("%s<priority>0.8</priority>\n", $result);
			$result = sprintf("%s</url>\n", $result);
		}
	}
	$result = sprintf("%s</urlset>", $result);
	header('HTTP/1.1 200 OK');
	header('X-Robots-Tag: noindex, follow', true);
	header('Content-Type: text/xml');
	printf("%s\n", $result);
	exit();
}

//
// Invoke plugin if XML sitemap requested.
//
if (preg_match('#/sitemap\.xml$#', $_SERVER['REQUEST_URI']) == 1) {
	add_action('template_redirect', 'chartis_sitemap');
}

?>