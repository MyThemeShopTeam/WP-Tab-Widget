=== WP Tab Widget ===
Contributors: mythemeshop
Creator's website link: http://mythemeshop.com/
Tags: tabs, tab widget, recent posts tab, tabs widget, ajax tabs, ajax widget.
Requires at least: 4.0
Tested up to: 4.9.5
Stable tag: 1.2.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Tab Widget is the AJAXified plugin which loads content by demand, and thus it makes the plugin incredibly lightweight.

== Description ==

We know you always loved those tab widgets which have that lazy loading effect in them. We at <a href="http://mythemeshop.com/">MyThemeShop</a> understand your need, and have developed a unique, cleanly coded, premium tab plugin. We are now distributing it for FREE to give back to the WordPress community. We have been given so much by the WordPress, it's the time to pay back.

**WP Tab plugin** is the only plugin you need to get the perfect tabs on your blog. We have made it AJAXified, so the content loads only when demanded, and thus it makes the plugin incredibly lightweight. It loads before you could even blink your eye. If you're a website owner, you always want your visitors to stay longer on your website. With WP Tab plugin, you could do it in a simple way. Install the plugin, configure the widget and let your visitors find the best content on your website in the sidebar without struggling to actually search for it.

= Live demos: =
See WP Tab Widget in action on our demo pages:
<a href="http://demo.mythemeshop.com/truepixel/">http://demo.mythemeshop.com/truepixel/</a>

= Why WP Tab from <a href="http://mythemeshop.com/">MyThemeShop</a>: =
* It's the only free plugin which offers so many features
* It loads the content by demand
* Choose between, Popular, Recent, Comments, Tags tab
* In-built Pagination System
* Fully Responsive
* Control the order of the tabs
* Change the number of tabs to show
* Control the number of posts to show
* Super light weight
* In-built cache system, once a tab is loaded, it stays in the memory
* Cool effects
* Easy to modify the CSS to better fit your theme style
* Choose between 3 unique styles of small, big or no thumbnails
* Show/Hide post date
* Show/Hide number of comments
* Show/Hide post excerpt
* Position it anywhere where a widget is configured in your theme.

= Support =

All support for this plugin is provided through our forums. If you have not registered yet, you can do so here for **FREE** <br>
<a href=“https://mythemeshop.com/#signup”>https://mythemeshop.com/#signup</a>

If after checking our Free WordPress video tutorials here:<br>
<a href=“https://mythemeshop.com/wordpress-101/”>https://mythemeshop.com/wordpress-101/</a><br>
&<br>
<a href=“https://community.mythemeshop.com/tutorials/category/2-free-video-tutorials/“>https://community.mythemeshop.com/tutorials/category/2-free-video-tutorials/</a><br>
<br>
you are still stuck, please feel free to open a new thread, and a member of our support team will be happy to help.<br>

Support link:<br>
<a href=“https://community.mythemeshop.com/forum/11-free-plugin-support/”>https://community.mythemeshop.com/forum/11-free-plugin-support/</a><br>
<br>

= Help to make it better =

MyThemeShop is a premium WordPress theme provider and we develop premium plugins in our free time and distribute them for free to give back to the community. Though we take a lot of care while developing anything, we might have missed something useful/important. Please help us make it better by submitting the bug/suggestions/feedback on GitHub.

GitHub link: <a href="https://github.com/MyThemeShopTeam/WP-Tab-Widget">https://github.com/MyThemeShopTeam/WP-Tab-Widget</a>

= Feedback =
If you like this plugin, then please leave us a good rating and review.<br> Consider following us on <a rel="author" href="https://plus.google.com/+Mythemeshop/">Google+</a>, <a href="https://twitter.com/MyThemeShopTeam">Twitter</a>, and <a href="https://www.facebook.com/MyThemeShop">Facebook</a> 

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the `wp-tab-widget` folder to the to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You can see **WP Tab Widget by MyThemeShop** widget in widgets section.
4. Add it in sidebar and footer and configure as you want.
5. Enjoy!

== Frequently Asked Questions ==

= Plugin is not working =

Please disable all plugins and check if shortcode plugin is working properly. Then you can enable all plugins one by one to find out which plugin is conflicting with plugin.

= Plugins is consuming too much resources =

The pageview is incremented on the database everytime a user visits a page but this can start getting quite expensive on high-traffic websites. We've added the filter `wpt_sampling_rate` which you can use to lower the sampling rate from the default <strong>100%</strong>. Note that setting this value too low will result to innacurate pageviews.

`
<?php

function wpt_my_sampling_rate( $rate ) {
    // Reduce the percentage of pageviews recorded to 80%.
    return 80;
}
add_filter( 'wpt_sampling_rate', 'wpt_my_sampling_rate' );
`

== Screenshots ==

1. WP Tab Widget Settings
2. WP Tab Widget

== Changelog ==

= 1.2.8 (April 15, 2018) =
* Fixed compatibility with PHP 7
* Checked compatibility with WordPress v4.9.5
* Improved Code

= 1.2.7 (Feb 03, 2017) =
* Added new wpt_sampling_rate filter. See the FAQ section on how this works.

= 1.2.6 (Jan 28, 2017) =
* Updated view counter function

= 1.2.5 (Apr 06, 2016) =
* Added missing Pro version image

= 1.2.4 (Mar 27, 2016) =
* Replaced “comm” CSS class with “comments-number” on comment count in Recent tab
* Added “Show Some Love” option

= 1.2.3 (Mar 22, 2016) =
* Fixed issue where tab content couldn’t be loaded on servers with non UTF8 character encoding
* List only comments from default comment type in Comments tab
* Prevent conflict with Pro version
* Added Notification & Banner for Pro Version

= 1.2.2 (Aug 21, 2015) =
* Changed text domain to make plugin compatible with WordPress Language Packs

= 1.2.1 (Aug 19, 2015) =
* Switched to PHP 5 style constructor method for the widget class

= 1.2 (Oct 15, 2014) =
* Added Title Length option
* Fixed post view count compatibility with themes

= 1.1 (Oct 1, 2014) =
* Popular posts will be decided by number of views
* Added loading effect
* Fixed small bugs
* Performance improvement

= 1.0 (Mar 24, 2014) =
* Plugin released
