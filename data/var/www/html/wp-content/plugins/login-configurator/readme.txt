=== Login Configurator ===
Contributors: grandslambert
Donate link: http://grandslambert.tk/plugins/login-configurator.html
Tags: login, login redirect, customize login, login notes
Requires at least: 2.5
Tested up to: 3.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add features to control how your login form works including where the user is redirected when they log in.

== Description ==

Change the way your login functions work including forcing users to log in, changing the URL they go to when the login is successful, adding text to the login form, and change the logo and link on the login form.

Features include:

* Force users to log in to the site.
* Can also protect your feed URL.
* Set a default redirect URL when they log in.
* Add a message box with title to the login form.
* Set the background image to use on the login form, and the repeat rule.
* Set the URL for a new logo for all login pages.
* Set the URL for the link on the logo for all login pages.
* Whitelist certain URLs in the site to remain unprotected.

== Installation ==

1. Upload `login-configurator` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin on the Settings->Login Configurator screen.

== Changelog ==

= 2.0 - December 28th, 2012 =

* Fixed a bug where the logo no longer was replaced.
* Fixed the whitelist URL box so that the site url is ignored if included.
* Fixed some links in the plugin to point to grandslambert.tk
* Added the ability to change the background of the login page.

= 1.6 - Feburary 11th, 2011 =

* Fixed the loading of the stylesheets and javascript.
* Fixed a bug in the whitelist URL code that did not work with all URLs.
* Cleaned up the code to reduce load.
* Added language file and support for translations.

= 1.5 - December 18th, 2009 =

* Cleaned up some code causing errors in HTML code.
* Fixed an issue with CSS properties on the login page.

= 1.4 - December 17th, 2009 =

* Fixed an issue where the plugin failed on Wordpress MU.
* Fixed an issue where the logo link setting did not work.
* Fixed a bug in whitelist URLs to use all URLs entered. URLs should be entered starting with the first / after the domain name.
* Added a title field to add to the login form.
* Added styles for the title and text forms.
* Now saves all option data in one option record, converts 1.3 and previous settings on activation.

= 1.3 - November 20th, 2009 =

* Fixed an issue where "Teaser" option does not work with a static home page.

= 1.2 - October 16th, 2009 =

* Fixed an issue with the last whitelist URL not being recognized properly.

= 1.1 - September 15th, 2009 =

* Fixed a small bug that broke installation on some versions of PHP.

= 1.0 - July 19th, 2009 =

* Added an option to choose whether or not to protect the feed URL.
* Added a whitelist feature to prevent plugin from affecting certain URLs

= 0.6.1 - February 14th, 2009 =

* Fixing the plugin to work with PHP 4 - was originally only compatible with PHP5
* Changing the version numbering system to better match the version.

= 0.6.0 - February 13th, 2009 =

* This minor fix removes some errors in the settings screen and fixes the headers already sent in some themes.

= 0.5.0 - February 13th, 2009 =

* Early alpha release

== Upgrade Notice ==

= 1.6 =
If you are using whitelist URLs you should update.

= 1.5 =
Not required, just some code cleanup and text changes.

= 1.4 =
Required update for latest version of Wordpress MU.

== Frequently Asked Questions ==

= Why do I need this plugin? =

If you have ever wanted to control how your login page looks and works, you need this plugin.

If you want to protect your blog, or parts of your blog, behind a login, you need this plugin.

If you want to change the logo on your blog's login pages, you need this plugin.

= Where can I get support? =

http://grandslambert.tk/support/forum/login-configurator

== Screenshots ==

1. The settings screen.