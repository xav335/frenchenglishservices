=== iQ Block Country ===
Contributors: iqpascal
Donate link: https://www.webence.nl/plugins/donate
Tags: spam, block, country, comments, ban, geo, geo blocking, geo ip, block country, block countries, ban countries, ban country, blacklist, whitelist, security
Requires at least: 3.5.2
Tested up to: 4.4.1
Stable tag: 1.1.27
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allow or disallow visitors from certain countries accessing (parts of) your website


== Description ==

iQ Block Country is a plugin that allows you to limit access to your website content.
You can either allow or disallow visitors from defined countries to (parts of) your content.

For instance if you have content that should be restricted to a limited set of countries you can do so.
If you want to block rogue countries that cause issues like for instance hack attempts, spamming of your comments etc
you can block them as well.

Do you want secure your WordPress Admin backend site to only your country? Entirely possible! You can even
block all countries and only allow your ip address.

And even if you block a country you can still allow certain visitors by whitelisting their ip address just
like you can allow a country but blacklist ip addresses from that country.

You can show blocked visitors a message which you can style by using CSS or you can redirect them to a page 
within your WordPress site. Or you can redirect the visitors to an external website.

You can (dis)allow visitors to blog articles, blog categories or pages or all content.

Stop visitors from doing harmful things on your WordPress site or limit the countries that can access your
blog. Add an additional layer of security to your WordPress site.

This plugin uses the GeoLite database from Maxmind. It has a 99.5% accuracy so that is pretty good for a free database. If you need higher accuracy you can buy a license from MaxMind directly.
If you cannot or do not want to download the GeoIP database from Maxmind you can use the GeoIP API website available on http://geoip.webence.nl/

If you want to use the GeoLite database from Maxmind you will have to download the GeoIP database from MaxMind directly and upload it to your site.
The Wordpress license does not allow this plugin to download the MaxMind Geo database for you.

= Using this plugin with a caching plugin =

 Please note that many of the caching plugins are not compatible with this plugin. The nature of caching is that a dynamically build web page is cached into a static page.
 If a visitor is blocked this plugin sends header data where it supplies info that the page should not be cached. Many plugins however disregard this info and cache the
 page or the redirect. Resulting in valid visitors receiving a message that they are blocked. This is not a malfunction of this plugin.

== Installation ==

1. Unzip the archive and put the `iq-block-country` folder into your plugins folder (/wp-content/plugins/).
2. Download the IPv4 database from: http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
3. Unzip the GeoIP database and upload it to your upload dir usually /wp-content/uploads/GeoIP.dat
4. Download the IPv6 database if you have a website running on IPv6 from: http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz
5. Unzip the GeoIP database and upload it to your upload dir usually /wp-content/uploads/GeoIPv6.dat
6. If you do not want to or cannot download the MaxMind GeoIP database you can use the GeoIP API.
7. Activate the plugin through the 'Plugins' menu in WordPress
8. Go to the settings page and choose which countries you want to ban. Use the ctrl key to select multiple countries

== Frequently Asked Questions ==

= How come that I still see visitors from countries that I blocked in Statpress or other statistics software? =

It’s true that you might see hits from countries that you have blocked in your statistics software. 

This however does not mean this plugin does not work, it just means somebody tried to access a certain page or pages and that that fact is logged.

If you are worried this plugin does not work you could try to block your own country or your own ip address and afterwards visit your frontend website and see if it actually works. Also if you have access to the logfiles of the webserver that hosts your website  you can see that these visitors are actually denied with a HTTP error 403.

= How come I still see visitors being blocked from other security plugins? =

Other wordpress plugins handle the visitors also. They might run before iQ Block Country or they might run after iQ Block Country runs.

This however does not mean this plugin does not work, it just means somebody tried to access a certain page, post or your backend and another plugin also handled the request.

If you are worried this plugin does not work you could try to block your own country or your own ip address and afterwards visit your frontend website and see if it actually works. Also if you have access to the logfiles of the webserver that hosts your website  you can see that these visitors are actually denied with a HTTP error 403.


= This plugin does not work, I blocked a country and still see visitors! =

Well, this plugin does in fact work but is limited to the data MaxMind provides. Also in your statistics software or logfiles you probably will see log entries from countries that you have blocked. See the "How come I still see visitors..." FAQ for that.

If you think you have a visitor from a country you have blocked lookup that specific IP address on the tools tab and see which country MaxMind thinks it is. If this is not the same country you may wish to block the country that MaxMind thinks it is.

= Whoops I made a whoops and blocked my own country from visiting the backend. Now I cannot login... HELP! =

I am afraid this can only be solved by editing your MySQL database,directly editing the rows in the wp_options table. You can use a tool like PHPMyAdmin for that.

If you don't know how to do this please ask your hosting provider if they can help, or ask me if I can help you out!

= Why do you not make something that can override that it blocks my country from the backend. =

Well, if you can use a manual override so can the people that want to 'visit' your backend. 

This plugin is meant to keep people out. Perhaps you keep a key to your house somewhere hidden in your garden but this plugin does not have a key somewhere hidden... So if you locked yourself out you need to call a locksmith (or pick the lock yourself of course!)

= How can I style the banned message? =

You can style the message by using CSS in the textbox. You are also able to include images, so you could visualize that people are banned from your site.

You can also provide a link to another page explaining why they might be banned. Only culprit is that it cannot be a page on the same domain name as people would be banned from that page as well.

You can use for instance:

<style type="text/css">
  body {
    color: red;
    background-color: #ffffff; }
    h1 {
    font-family: Helvetica, Geneva, Arial,
          SunSans-Regular, sans-serif }
  </style>

<h1>Go away!</h1>

you basicly can use everything as within a normal HTML page. Including images for instance.

= Does this plugin also work with IPv6? =

Since v1.0.7 this plugin supports IPv6. IPv6 IP addresses are more and more used because there are no new IPv4 IP addresses anymore.

If your webhosting company supplies your with both IPv4 and IPv6 ip addresses please also download the GeoIPv6 database or use the GeoIP API service.

If your webhosting company does not supply an IPv6 IP address yet please ask them when they are planning to.

= Why is the GeoLite database not downloaded anymore ? =

The Wordpress guys have contacted me that the license of the MaxMind GeoLite database and the Wordpress license conflicted. So it was no longer
allowed to include the GeoLite database or provide an automatic download or download button. Instead users should download the database themselves
and upload them to the website.

Wordpress could be held liable for any license issue. So that is why the auto download en update was removed from this plugin.

= Does this plugin work with caching? =

In some circumstances: No

The plugin does it best to prevent caching of the "You are blocked" message. However most caching software can be forced to cache anyway. You may or may not be able to control the behavior of the caching method.

The plugin does it bests to avoid caching but under circumstances the message does get cached.
Either change the behavior of your caching software or disable the plugin.

= How can I select multiple countries at once? =

You can press the CTRL key and select several countries.

Perhaps also a handy function is that you can type in a part of the name of the country!

You can select/deselect all countries by selecting "(de)select all countries..."

If you just want to allow some countries you can also use the invert function by selecting the countries you want to allow and select invert this selection.

= How can I get a new version of the GeoIP database? =

You can download the database(s) directly from MaxMind and upload them to your website.

1. Download the IPv4 database from: http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
2. Unzip the GeoIP database and upload it to your upload dir usually /wp-content/uploads/GeoIP.dat
3. Download the IPv6 database if you have a website running on IPv6 from: http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz
4. Unzip the GeoIP database and upload it to your upload dir usually /wp-content/uploads/GeoIPv6.dat

Maxmind updates the GeoLite database every month.

= I get "Cannot modify header information - headers already sent" errors =

This is possible if another plugin or your template sends out header information before this plugin does. You can deactivate and reactivate this plugin, it will try to load as the first plugin upon activation.

If this does not help you out deselect "Send headers when user is blocked". This will no longer send headers but only display the block message. This however will mess up your website if you use caching software for your website.

= What data get sends to you when I select "Allow tracking"? =

If you select this option each hour the plugin checks if it has new data to send back to the central server. 

This data consists of each IP address that has tried to login to your backend and how many attempts were made since the last check.

Goal of this feature is to check if we can create a user-driven database of rogue IP addresses that try to login to the backend.

If storing or sharing an IP address is illegal in your country do not select this feature.

= The laws in my country do not allow storing IP addresses as it is personal information. =

You can select the option on the home tab "Do not log IP addresses" to stop iQ Block Country from logging IP addresses. This will however also break the statistics.

= I have moved my WordPress site to another host. Now iQ Block Country cannot find the GeoIP databases anymore =

Somewhere in your WordPress database there is a wp_options table. In the wp_options table is an option_name called 'upload_path'.

There probably is an (old) path set as option_value. If you know your way around MySQL (via PHPMyAdmin for instance) you can empty the option_value.
This should fix your problem.

Please note that your wp_options table may be called differently depending on your installation choices.


== Changelog ==

= 1.1.27 =

* Bugfix: Fixed small bug

= 1.1.26 =

* New: xmlrpc.php is now handled the same way as other backend pages.
* Change: Updated chosen library to latest version.
* Change: Added a (de)select all countries to the backend en frontend country list.
* Change: Changed order of how the plugin detects the ip address.
* Change: Added detection of more header info that can contain the proper ip address
* New: Added support forum to the site.
* Change: Added download urls on database is too old message.

= 1.1.25 =

* Bugfix: Altered checking for Simple Security Firewall

= 1.1.24 =

* New: Added support for Lockdown WordPress Admin
* New: Added support for WordPress Security Firewall (Simple Security Firewall)
* Change: Various small changes

= 1.1.23 =

* Bugfix: Fixed bug if cURL was not present in PHP version
* New: When local GeoIP database present it checks if database is not older than 3 months and alerts users in a non-intrusive way.

= 1.1.22 =

* Bugfix: Category bug squashed
* Change: Altered text-domain
* New: Added export of all logging data to csv. This exports max of 1 month of blocked visitors from frontend & backend.

= 1.1.21 =

* Change: Minor improvements
* New: Added check to detect closest location for GeoIP API users
* Bugfix: Fixed an error if you lookup an ip on the tools tab while using the inverse function it sometimes would not display correctly if a country was blocked or not.
* New: Added support for All in one WP Security Change Login URL. If you changed your login URL iQ Block Country will detect this setting and use it with your backend block settings.

= 1.1.20 =

* New: Added Google Ads to search engines
* New: Added Redirect URL (Basic code supplied by Stefan)
* New: Added inverse selection on frontend. (Basic code supplied by Stefan)
* New: Added inverse selection on backend.
* New: Validated input on the tools tab.

= 1.1.19 =

* Bugfix: Check if MaxMind databases actually exist.
* New: Unzip MaxMind database(s) if gzip file is found.
* New: Block post types
* New: Added option to select if you want to block your search page.
* New: When (re)activating the plugin it now adds the IP address of the person activating the plugin to the backend whitelist if the whitelist is currently empty.

= 1.1.18 =

* Change: Changed working directory for the GeoIP database to /wp-content/uploads

= 1.1.17 =

* Change: Due to a conflict of the license where Wordpress is released under and the license the MaxMind databases are released under I was forced to remove all auto downloads of the GeoIP databases. You now have to manually download the databases and upload them yourself.
* New: Added Webence GeoIP API lookup. See http://geoip.webence.nl/ for more information about this API.

= 1.1.16 =

* New: Accessibility option. You can now choose if you want the country default selectbox or an normal selectbox.
* New: New button to empty the logging database..
* New: You can now set the option to not log the ip addresses to the database. This does not influence the blocking process only the logging process. This can be handy if the laws in your country do not permit you to log this information or if you choose not to log this information

= 1.1.15 =

* Bugfix: You can now set an option to buffer the output of the iQ Block Country plugin. If you use for instance NextGen Gallery you should not set this option as it will break uploading pictures to your gallery.
* Bugfix: Last time GeoIP databases were downloaded was wrong.
* Bugfix: If you configured auto-update of the GeoIP databases the tools tab showed that you did not configure auto update.
* Added check for HTTP_X_TM_REMOTE_ADDR to get real ip address of T-Mobile users.
* Added Twitter, Bitly, Cliqz and TinEye to the search engines list.
* New: No longer blocks category pages of categories you have not blocked.
* Bugfix: Added check if HTTP_USER_AGENT is set.

= 1.1.14 =

* Bugfix: The plugin did not recognise the login page when installed to a subdirectory.
* New: You can configure if it auto updates the GeoIP Database. Upon request of those people who have the paid database of MaxMind.
* Added Facebook and MSN to list of search engines.
* Changed the version of the geoip.inc file to the version of https://github.com/daigo75/geoip-api-php

= 1.1.13 =

* Bugfix on setting defaults when they values already existed.
* You can now allow search engines access to your country even if they come from countries that you want to block.

= 1.1.12 = 

* Bugfix on the backend blacklist / whitelist

= 1.1.11 =

* Added select box on how many rows to display on the logging tab
* Redirect blocked users to a specific page instead of displaying the block message.
* Added blacklist and whitelist of IP addresses to the backend.
* Adjusted some text
* Minor bugfixes

= 1.1.10 =

* Small fixes
* WP 3.9 compatability issue fixed

= 1.1.9 =

* Bugfix release due to faulty v1.1.8 release. My Apologies.

= 1.1.8 =

* Smashed a bug where the homepage was unprotected due to missing check.

= 1.1.7 =

* Added Russian (ru_RU) translation by Maxim
* Added Serbo-Croatian (sr_RU) translation by Borisa Djuraskovic (Webostinghub)
* Changed the logging table a bit.

= 1.1.6 =
* Added to ban categories. This works the same way as blocking pages (By request of FVCS)
* Changed the admin page layout. Added tabs for frontend and backend blocking to make it look less cluttered
* Added optional tracking to the plugin. This is an experiment to see if building a database of IP addresses that try to login to the backend is viable.
* Upon first activation the plugin now fills the backend block list with all countries except the country that is currently used to activate.
* Added IP checking in header HTTP_CLIENT_IP and HTTP_X_REAL_IP

= 1.1.5 =

* Statistics required wp-config.php in a specific place bug smashed.

= 1.1.4 =

* Added import/export function.
* Minor bugs solved

= 1.1.3 = 

* Fixed error that when using the option to block individual pages all visitors would be blocked. (Thanks to apostlepoe for reporting)

= 1.1.2 =

* Fixed localization error. (Thanks to Lisa for reporting)

= 1.1.1 =

* You can now choose to block individual pages. Leaving other pages open for visitors from blocked countries. You can for instance use this feature to block countries from visiting specific pages due to content rights etc.
* Source now supports localization. Included is the English and Dutch language. I'd be happy to include other translations if anyone can supply those to me.

= 1.1 =

* Added statistics to the plugin.
* You can view the last 15 hosts that were blocked including the url they visited.
* You can view the top 15 of countries that were blocked in the past 30 days.
* You can view the top 15 of hosts that were blocked in the past 30 days.
* You can view the top URL's that were most blocked in the past 30 days.

== Upgrade Notice ==

= 1.1.19 =

This plugin no longer downloads the MaxMind database. You have to download manually or use the GeoIP API.