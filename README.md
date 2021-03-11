# WP Themes & Plugins Stats #
**Contributors:** [brainstormforce](https://profiles.wordpress.org/brainstormforce)  
**Donate link:** https://www.paypal.me/BrainstormForce  
**Tags:** active-install, stats, themes-stats, plugin-stats, total download & active install count of plugin and theme by author.      
**Requires at least:** 4.2  
**Requires PHP:** 5.2  
**Tested up to:** 5.7  
**Stable tag:** 1.1.1  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html   

## Description ##

The WP Themes & Plugins Stats plugin automatically fetch theme and plugin stats ( name, active installs, 5-star ratings, etc. ) from the WordPress.org API and store it. These stats can be shown anywhere on the website using shortcodes. 

Displaying real numbers build trust for visitors! 

Let's say you compared two WordPress themes along with some statistical parameters like - Active Installs, 5 Star Ratings, Total Downloads, etc. As these values keep changing every day it was a manual work to copy & paste values from the WordPress.org repository. But not anymore! 

The WP Themes & Plugins Stats plugin provides an easy and simple way to display stats with shortcodes. You just need to add shortcode once and the plugin will automatically update the values with WordPress.org API.

Display Stats Using Shortcodes

## Different shortcodes are available to display the following counts - for both - Theme and Plugins from WordPress.org repository. ##

+ Theme/Plugin Name 
+ Total Active Installs
+ Last Updated       
+ Theme/Plugin Version       
+ Theme/Plugin Ratings Count
+ 5 Star Ratings Count
+ Average Ratings in Number
+ Average Ratings in Star
+ Total Downloads
+ Download   
+ Total Active Installation of All Themes/Plugins [For authors]
+ Total Download Count of All Themes/Plugins [For authors]

## The plugin provides global settings under the “General ” tab. You can format the stat value/ number. ##

1. Set an interval to check and update stats values 
As mentioned earlier stats keep changing every day. You can choose a number of days for updations. The plugin will fetch the latest stats after this interval and update values on the website automatically.  

2. Choose the stat value format to make it easily readable
 Usually, installation & download count is a big number. So it is quite hard to count and read the actual value. Simplifying this large number with notations make it more readable like - a number 1,000,000 can be displayed as 1M/1 Million or 1000K/ 1000 Thousand. 
Moreover, you can select a format to group numbers, like -  1,000,000 (comma) or 1.000.000 (dot)

3. Set a date format as per convenience 
WordPress default date formats are available for customizations. 

Connect to theme/plugins with WordPress.org API

## Choosing a theme/plugin to fetch stats from is very simple. ##

1. Visit theme/plugin on WordPress.org repository
2. Copy the slug
3. Add it in a shortcode 

The plugin will fetch stats via slug. 

## How To Use This Plugin? ##

Once this plugin is installed, you can customize it under Settings > WP Themes & Plugins Stats.

Step 1: Under the General tab, manage the global stats number format. Set the required parameters, like update interval, count format, date format. 
Step 2: From the shortcodes tab, choose and copy the required code.
Step 3: Paste it on a required page/post.  
Step 4: Add a slug/author name for theme/plugin.

That's it! Visit Post/Page to see results.

These shortcodes can be added in any page builder including, Elementor, Beaver Builder, etc.
You can use the shortcode multiple times on a page. 

## Screenshots ##
1. Global settings under the 'General' tab
2. Getting Started help under 'Shortcodes' tab
3. All available shortcodes are listed under 'Shortcodes' tab
4. List of shortcodes and their outputs for theme
5. List of shortcodes and their outputs for plugin

## Changelog ##

### 1.1.1 ###
- Fix : Added compatibility with WordPress v5.7 for jQuery migration warnings on the admin page.
- Fix : PHP Warnings for empty ratings and total counts.

### 1.1.0 ###
- New : Optimized the code.
- Fix : Transient not Updating for themes/plugins slug.

### 1.0.1 ###
- New : New shortcode for themes/plugins to display star ratings.
- Fix : Wrong Transient set for themes/plugins slug.

### 1.0.0 ###
- Initial release

