# Chartis

Ensure that search engines know about all the _pages_, _posts_ and _custom post types_ on your site with this simple, dynamic XML sitemap generator. Why this plugin name? Chartis means "map" in Greek.

## Professional Support

If you need professional plugin support from me, the plugin author, contact me via my website at http://lutrov.com

## Copyright and License

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.

## Documentation

This plugin provides an API to customise the default constant values. See this example:

	// ---- Change the Chartis plugin custom post types to include in the sitemap.
	add_filter('chartis_post_types_filter', 'custom_chartis_post_types_filter');
	function custom_chartis_post_types_filter($types) {
		foreach (array('movie', 'book', 'product') as $type) {
			array_push($types, $type);
		}
		return $types;
	}

Or if you're using a custom site plugin (you should be), do it via the `plugins_loaded` hook instead:

	// ---- Change the Chartis plugin constant values.
	add_action('plugins_loaded', 'custom_chartis_filters');
	function custom_chartis_filters() {
		// Change the custom post types to include in the sitemap.
		add_filter('chartis_post_types_filter', 'custom_chartis_post_types_filter');
		function custom_chartis_post_types_filter($types) {
			foreach (array('movie', 'book', 'product') as $type) {
				array_push($types, $type);
			}
			return $types;
		}
	}

Note, this second approach will _not_ work from your theme's `functions.php` file.
