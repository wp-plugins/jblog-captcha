=== JBlog Captcha ===
Contributors: alonelion1987
Official website: http://jblog-project.ru/kapcha-v-polzovatelskix-formax/
Tags: anti-spam, antispam, anti-spam security, captcha, custom form captcha, symbol captcha, security custom form, custom captcha, capcha, develop captcha, kit captcha
Requires at least: 4.2
Tested up to: 4.2.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generated CAPTCHA numbers and Latin characters. It is intended only for use in custom forms and displays a simple shortcode.

== Description ==

JBlog Captcha generates a picture with the characters and the text box. It is possible to choose the background for the image and change the font color.

The documentation provided with comprehensive information on the methods of treatment of user data sent to the server.

[Official plugin page](http://jblog-project.ru/kapcha-v-polzovatelskix-formax/)

== Installation ==

1. Activate the plugin through the "Plugins" menu in WordPress.
2. Go to setting `settings/JBlog Captcha` page and change the default settings.


== Frequently Asked Questions ==

= How do I display the form online captcha =

You have to use shortcode to check: `<?php if(class_exists('JBlogCaptcha')){print do_shortcode("[jbcptch]");} ?>`

= What functions can I use the test to check if the user entered the captcha or not? =

The file handler on the server side, you must define the variable `$ _POST ['str']`, which you pass the captcha value entered by the user;
In the file - handler: the condition before `... if () {}`, in which the check data, use the instructions: `JBlogCaptcha::instance()->chekSession();`, which checks whether the correct CAPTCHA introduced;
In the file - handler: in the condition `if (var_1 && var_2 && ...) {}`, use the instructions: `JBlogCaptcha::instance()->getChek()`, which returns true if the CAPTCHA is entered correctly , along with a test of its own data;
In the file - handler: body conditions under which returns true, `if () {...}`, at the end, use the instructions: `JBlogCaptcha::instance()->refresh();`, which resets the CAPTCHA to its original state.

== Screenshots ==

1. Admin panel settings page.
2. CAPTCHA concluded in the public section.

== Changelog ==

* 1.0 version of the repository

== Upgrade Notice ==

1.0 version start