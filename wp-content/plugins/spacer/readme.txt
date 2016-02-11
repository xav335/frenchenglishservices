=== Spacer ===
Contributors: clevelandwebdeveloper
Donate link: http://www.clevelandwebdeveloper.com/wordpress-plugins/donate.php
Tags: spacer, spacing, line space
Requires at least: 3.5
Tested up to: 4.4
Stable tag: 3.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a spacer button to the WYSIWYG visual editor which allows you to add precise custom spacing between lines in your posts and pages. 

== Description ==

This plugin adds a spacer button to the WYSIWYG visual editor which allows you to add precise custom spacing between lines in your posts and pages. Note that you can also enter negative spacing to shift the following content upwards.

Via the Spacer Settings page, you can create an arsenal of additional spacers and sectional breaks to have at your disposal as you lay out your pages. The Spacers that you build can be added into your posts and pages with the spacer button in the WYSIWYG visual editor (see screenshots).

Spacer is also compatible with WordPress Multisite.

<h3>New in Version 3.0</h3>

<ul>
<li>Create an <strong>unlimited</strong> amount of spacers, dividers, and section break presets. Give your new Spacers names and save them for later.</li>
<li>Live preview tool as you build your Spacers.</li>
<li>Choose which Spacer you want to add via the spacer button in the WYSIWYG visual editor.</li>
</ul>

== Installation ==

1. From WP admin > Plugins > Add New
1. Search "Spacer" under search and hit Enter
1. Click "Install Now"
1. Click the Activate Plugin link

== Frequently asked questions ==

= Why shouldn't I just press enter for new lines? =

Every time you press Enter or Shift-Enter you will get line breaks that are a certain specific height. Some times you want control over the exact amount of space between lines.

= How do I hide spacer on mobile screens? =

Settings > Spacer > Default Spacer Height On Mobile > Set this to 0

= How do I add a spacer to a page/post? =

Press the spacer button in WYSIWYG visual editor, then choose which spacer you want to add (see screenshot). This will add a shortcode [spacer height="20px"]. If you choose a custom Spacer, it will add something like [spacer height="40px" id="19"]

= How do I manually set the spacer height on individual spacers? =

After you add a spacer via the spacer button in WYSIWYG visual editor, you can change the height in the shortcode. For example, let's say you start off with the shortcode [spacer height="20px"]. Change 20px to whatever your desired line spacing is. For example, [spacer height="30px"] will give you 30 pixels of extra line spacing. If you use negative values the following content will be shifted upwards.

= How do I manually edit the height, mobile height, classes, and inline style on individual spacers? =
Here's an example of how you could apply this:
<pre>[spacer height="30px" mheight="0px" class="myspacer" style="background-color:red;"]</pre>

= How do I create additional Spacers =

Settings > Spacer > Add Spacers (top tab) > New Spacer

= How do I give my Spacer a background color? =

Try adding something like <code>background:gray;</code> to the Spacer's Style setting.

= I'm trying to create a divider, but the space above the divider is much more than the space below it. What can I do? =

Try adding <code>margin-bottom: 25px;</code> to the Spacer's Style setting.

== Screenshots ==

1. Live preview tool
1. Create an arsenal of spacers and sectional breaks to have at your disposal as you lay out your pages.
1. When writing your posts and pages, you can choose which Spacer you want to add via the Spacer button.
1. Showcasing some examples of Spacers and sectional breaks you can create.
1. Spacer Button
1. Setting the spacer height
1. Spacer height: 35px
1. Spacer height: -35px

== Changelog ==

= 3.0 =
* You can now create an arsenal of additional spacers and sectional breaks to have at your disposal as you lay out your pages.
* Give your new Spacers names and save them for later.
* Use the new live preview tool to help visualize the Spacer as you build it.
* When writing your posts and pages, you can choose which Spacer you want to add via the Spacer button.

= 2.0.3 =
* Added dividers on settings page to group the config options logically.
* Text internationalized so the plugin can easily by translated into other languages.
* Added translations for Spanish and Chinese.

= 2.0.2 =
* Resolved 'dismiss notice' bug for users without access

= 2.0.1 =
* Shortcode changed to use actual number (eg: [spacer height="20px"]), which will be easier to edit as you work
* 'Dismiss notice' link changed to red to make it more visible.
* Added compatibility with Beaver Page Builder's wysiswyg UI.

= 2.0 =
* Feature added: Choose to hide spacer on mobile devices, or choose a different spacer height for mobile.
* Feature added: Manually edit mobile height, custom classes, and inline style for individual spacer elements.
* Premium users: Speed up your workflow by setting a default height for your spacer. You can also set defaults for your spacer's custom classes and inline style.

= 1.0 =
* Initial version

== Upgrade Notice ==

= 3.0 =
New: You can now create an unlimited amount of spacers, dividers, and section breaks! Give them names and save them for later. Put them to good use as your style your posts and pages. Oh yeah, you also get a live preview tool to help visualize the Spacers as you build them. Enjoy!