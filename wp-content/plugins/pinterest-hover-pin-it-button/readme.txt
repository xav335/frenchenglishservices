=== Pinterest Hover Pin It Button ===
Contributors: billr.pinterest
Tags: social, images, plugin, pinterest
Requires at least: 3.0.1
Tested up to: 3.9
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This is the official Pinterest Hover Pin It button for Wordpress!
== Description ==

This is the official Pinterest Hover Pin It button for Wordpress. This plugin enables a Pin It button over all of your images that are at least 120x120.

For more information on the Pin It button, check out the [Business for Pinterest](http://business.pinterest.com/pin-it-button/) site.

== Installation ==

1. Upload `pin_it_button.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Settings -> Pin It Button to enable the hover Pin It button

== Frequently Asked Questions ==

= Can I change the size and color of my hover Pin It button? =

This is now available in version 1.0 and higher.

= My image is at least 120x120 and still doesn't have a hover Pin It button! =

The hover Pin It button still might not appear if the following situations are occurring:

* The image is referenced in your CSS or style tags as opposed to an `img src`
* The image is being displayed using a lightbox or other gallery plugin that uses AJAX
* There is some other sort of on-hover effect over the image
* The image has the `data-pin-no-hover="true"` attribute set
* The image has the `nopin="nopin"` attribute set
* Your image `src` starts with `data:`

= The Pin It button is offset when hovering over the image =

This is a known issue that affects the Pin It button on all Wordpress sites when a user is logged in. If you logout, or the user isn’t logged into the site, the Pin It button will appear in the correct spot. This will be fixed soon without any updates to the plugin needed.

= Where can I go for more help? =

You can write to us at our help center: http://help.pinterest.com

== Changelog ==

= 1.0 =
* Added round hover buttons
* Added red and white Pin It button colors (rectangular only)
* Added support for Japanese Pin It buttons (rectangular only)
* Added support for large and small Pin It buttons (rectangular only)
* Added async flag to pinit.js script line

= 0.9 =
* Initial version
