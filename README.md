# Chartis

Ensure that search engines know about all the _pages_, _posts_ and _custom post types_ on your site with this simple, dynamic XML sitemap generator. Why this plugin name? Chartis means "map" in Greek.

## Copyright and License

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.

## Documentation

This plugin automatically outputs "page", "post", "product", "course" post types but also provides an API to customise which post types get included. See this example:

	// ---- Change the Chartis plugin custom post types to include in the sitemap.
	add_filter('chartis_post_types', 'custom_chartis_post_types_filter');
	function custom_chartis_post_types_filter($types) {
		foreach (array('movie', 'book') as $type) {
			array_push($types, $type);
		}
		return $types;
	}


## Professional Support

If you need professional plugin support from me, the plugin author, contact me via my website at http://lutrov.com
