=== Coupon Matchup List Builder by My Coupon Database ===
Contributors: OodleTech
Tags: coupon, database, mcd, oodletech, matchup, list, builder
Stable tag: 1.0.3
Requires at least: 3.0
Tested up to: 3.3

The My Coupon Database Coupon List and Coupon Match plugin allows you to create customized lists and add coupon matches in one place.

== Description ==

The My Coupon Database Coupon List and Coupon Match plugin provides an integrated tool for users of the coupon database to quickly create customized lists and add coupon matches. Users of the plugin are current customers of the coupon database offered by MyCouponDatabase.com.

Plugin customers have reported a time savings of 50% or more with creating lists from store sale ads and adding coupon matches to each sale item. With the many customizable options, users can access the time-saving tools along with keeping their brand identity.

= Backend Features = 
* API driven
* Customize your "Print my List" page by adding a logo
* Your brand is only visible - there is no mention of the My Coupon Database brand
* Customize the footer of the "Print my List" page with an ad or other HTML
* Customize the blog format expiration field
* Customize the final pricing language
* Add and customize language to show before each coupon entry (i.e., STACK, USE, etc)
* Customize CSS Styling
* Create and customize categories
* Easy drag and drop to re-order categories quickly
* Add a list to your post with a shortcode
* Create customized lists
* Multiple item data entry for time efficiency
* Single item data entry
* Add coupon matches to list items on the dashboard
* Add your own coupons to the list
* AJAX technology provides potential coupon matches without keying in the item name
* Enter the final price for the item on the dashboard next to that item's name
* Edit item names or text
* Delete items from the list
* Import or Export coupon lists to other users of the database and plugin from My Coupon Database

= Front End Features =
* Users add items to their customizable list by clicking on each checkbox
* "Select all" or "Deselect all" to quickly add items to the coupon list
* My Coupon List dashboard holds all items and coupons so users can browse without forgetting
* Create printable list of all items with coupon matches so users can efficiently gather or print coupons
* Users can visit various posts with coupon matches and continue to add items to their list
* The My Coupon List dashboard holds all selected items and coupon matches. 
* Users can easily compare prices amongst stores to find the best price and delete other items from their list
* The "Print my List" option creates a new window so users do not lose their place on your site
* All items saved in My Coupon List will show up on separate pages for each store or post upon printing

== Installation ==

This section describes how to install the plugin and get it working.

= Easy Install =

1. Backup your WordPress Database! Never trust a plugin, even ours.
2. Go to the plugins screen with WordPress Admin Panel
3. Search "Coupon Matchup List Builder".
4. Install the "Coupon Matchup List Builder by My Coupon Database" plugin.
5. Activate Plugin
6. Enjoy!

= Manual Install =

1. Backup your WordPress Database! Never trust a plugin, even ours.
2. Upload `plugin-name.php` to the '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Click 'Current Lists' from the Left Hand Menu
5. Click on 'Settings' under 'Current Lists'
6. Enter API Key (Obtain from http://mycoupondatabase.com - Pro + Plugin Package)
7. Enjoy!

== Screenshots ==

1. screenshot-1.png 
2. screenshot-2.png 
3. screenshot-3.png 
4. screenshot-4.png 
5. screenshot-5.png

== Changelog ==

= 1.0.3 =
* Fixed an issue that was preventing certain environments from adding coupons to their list.

= 1.0.2 =
* Fixed an install bug where the "Default Categories" group wasn't being added on install.

= 1.0.1 =
* Converted the Import/Export feature to use JSON instead of XML for more compatible encoding.
* Completely re-wrote the import/export list scripts.
* Importing a list now also imports the category group and all the categories in that category group to allow for easier sharing of lists.
* Imported category groups will display on the categories screen and have the text (imported) after the category group name to note that category group was created via a list import.
* mySQL modification to the category groups table to denote origin of the category group.

= 1.0.0 =
* The plugin is now on the WordPress repository!
* Converted the print list feature to a lightbox instead of a popup to avoid confusion with pop up blockers.
* Increased browser compatibility for the print list feature.
* Fixed an enqueue script bug.
* Modified mySQL table structure to be more compatible across mySQL server environments.
