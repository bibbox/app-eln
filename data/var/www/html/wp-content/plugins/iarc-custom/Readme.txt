=== IARC Customization for ELN ===
Author: Lucile Alteyrac (IARC)
Last update: 19th of April 2017
Tested with WP4.7.3

=== Description ===
Provides custom to WP and some Plugins for IARC Electronic (Laboratory) Notebook

=== Dependencies ===

The following plugins are customized by 'IARC Customization for ELN'
* Category and authors
* Last Updated Posts Widget 0.5.1
* Post Notification	1.2.40
* TinyMCE Advanced 4.4.3

However, these plugins are not mandatory for the IARC ELN.

=== Theme ===

The IARC ELN uses the theme Atahualpa and a custom child theme Ataiarc
Tested version: Atahualpa 3.7.24

=== Plugins ===

= Mandatory Plugins for IARC ELN =

* Collapsing Categories 2.0.7, by Robert Felty
* Login Configurator 2.0, by GrandSlambert
* Role Scoper 1.4.1, by Kevin Behrens
* WP-PageNavi 2.89.1, by Lester 'GaMerZ' Chan

= Recommended Plugins =

* Category Wise Search Widget 1.3, by Shambhu Prasad Patnaik
* Last Updated Posts Widget 0.5.1, by Andrea Developer
* My Link Order 3.5, by Andrew Charlton
* Post Notification 1.2.40, by Moritz Strübe
* TinyMCE Advanced 4.4.3, by Andrew Ozz
* WP Favorite Posts 1.6.3, by Huseyin Berberoglu
* WP-Print 2.57.1, by Lester 'GaMerZ' Chan

= Useful Plugins =

* Category and authors, by Andreas Gros
* Clean WP Dashboard 1.0, by Jerod Santo
* CSS Columns 0.9.3, by Redwerks
* Dynamic to Top 3.4.2, by Matt Varone and Tim Berneman
* Heartbeat Control 1.0.3, by Jeff Matson
* Maintenance Mode 5.4, by Michael Wöhrer
* POST2PDF Converter 0.4.2, by Redcocker
* ThreeWP Activity Monitor 2.12, by Edward Mindreantre
* WassUp Real Time Analytics 1.8.7, by Michele Marcucci and Helene Duncker
* WP-UserOnline 2.87, by Lester 'GaMerZ' Chan

=== Widgets ===

- Collapsing Categories:
		provided by the plugin 'Collapsing Categories'
- Recent Posts:
		core widget
		customized by the plugin 'IARC Customization for ELN'
- Last Updated Posts Widget: 
		provided by the plugin 'Last Updated Posts Widget'
- Recent Comments:
		core widget
- Category Wise Search:
		provided by the plugin 'Category Wise Search Widget'
- My Link Order:
		provided by the plugin 'My Link Order', can be used several times
- Text: 
		core widget, used to build the 'Subscribe to Posts' box (see html code below)
        need the plugin 'Post Notification' to be functional and the theme Atahualpa
- Calendar:
		core widget
- User's Favorites:
		provided by the plugin 'WP Favorite Posts'

-- Code for the widget Text 'Subscribe to Posts'
<center><form id="newsletter" method="post" action="/post_notification_header/" >
<img width="15" margin-top="12px" src="/wp-content/themes/atahualpa/images/feedburner-email.gif"> 
<input title="insert your email address" type="text" name="addr" placeholder="email address" size="20" maxlength="60" style="font-size:11px;margin-bottom:1px;"/>
 <br/><input type="submit" name="submit" value="Subscribe" style="font-size:11px;"/></form>
</center>


-------------------------------------------------------------

=== Known issues ===

-- Error Not Found: The requested URL /the-address/ was not found on this server
Check .htaccess at the root of the website

# BEGIN WordPress                                                                                                                                                                
<IfModule mod_rewrite.c>                                                                                                                                                         
RewriteEngine On                                                                                                                                                                 
RewriteBase /                                                                                                                                                                    
RewriteRule ^index\.php$ - [L]                                                                                                                                                   
RewriteCond %{REQUEST_FILENAME} !-f                                                                                                                                              
RewriteCond %{REQUEST_FILENAME} !-d                                                                                                                                              
RewriteRule . /index.php [L]
</IfModule>

# END WordPress


-- Post Notification plugin

After a new install, one table could be missing in the database:
	post_notification_emails
You can manually add the table by going to your database and copying this into the SQL field:

CREATE TABLE IF NOT EXISTS wp_post_notification_emails` (
id int(11) NOT NULL AUTO_INCREMENT,
email_addr varchar(255) DEFAULT NULL,
gets_mail int(11) DEFAULT NULL,
last_modified timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
date_subscribed datetime DEFAULT NULL,
act_code varchar(32) DEFAULT NULL,
subscribe_ip int(10) unsigned NOT NULL DEFAULT '0',
PRIMARY KEY (id),
KEY id (id,gets_mail),
KEY email_addr (email_addr)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;`

