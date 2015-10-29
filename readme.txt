=== cbParallax ===

Tags: image, background, fullscreen, parallax, Hintergrund, Bild, Hintergrundbild

Requires at least: 3.9  
Tested up to: 4.3.1  
Stable tag: 0.2.6
Version: 0.2.6
Contributors: demispatti  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you to customize single pages, posts and products with an individual, optionally parallaxing fullscreen background image.

== Description ==

This plugin enables you to set a custom background image.  It supports vertical and horizontal parallax scrolling effect, as long as the image dimensions exceed the viewport dimensions.  Be aware that the image is never being resized, just moved around, so keep that in mind.  You can align the image on either side parallel to the parallax direction or centered. The offset defines the area for the parallax effect.  You can also choose from a couple of overlays if you like to.

== Features ==

- Custom background image
- Compatible with posts, pages and products
- Optional fullscreen background parallax effect
- Works vertically and, for fun, horizontally
- Supports and is supported by Nicescroll
- Various overlays to choose from

This plugin works with recent versions of Chrome, Firefox, Internet Explorer, Opera and Safari.

== Requirements ==

Your theme must support the core WordPress implementation of the [Custom Backgrounds](http://codex.wordpress.org/Custom_Backgrounds) theme feature.

In order to use the parallax feature, I decided to set the minimum required image dimensions to 1920px * 1200px, so that covers a fullHD screen with a slight vertical parallax movement ( Image height - viewport height, so 1200px - 1080px gives 120px offset to move the image. I hope you get the point here.)

Your theme's layout must be "boxed" somehow or an opacity should be added to the page content container for the background image to be seen.

PHP version 5.4 or above.

== Installation ==

1. Upload the `cb-parallax` folder to your `/wp-content/plugins/` directory.
2. Activate the "cbParallax" plugin through the "Plugins" menu in WordPress.
3. Edit a post to add a custom background.

== Frequently Asked Questions ==

= Where do I interact with this plugin? =

You will find the "cbParallax" meta box on edit screens for posts, pages and products.

= How does it work? =

Within the meta box, you could:

1. Choose a background image. Depending on its dimensions you will be presented with a switch for enabling the parallax effect.
2. Leave the switch off, since you should first set the options for a static state.
3. Set the options below as you need.
4. Turn on the switch.
5. Set the options below as you need.

Save your work and visit the page :-)

= What was that about "Nicescroll"? =

I really like Nicescroll. I like its scroll behaviour, its momentum-scroll, its easing,... smile. It is perfect for parallaxing.  So I implemented it for a smooth cross-browser scrolling experience. I modified it slightly, tough, to preserve the browser-specific default vertical scrollbar. If you bring your "own" Nicescroll library, which could be the case with other plugins or your theme, this mod will not be loaded. So there won't be any conflict regarding this popular library, preserving it's unique scrolling behaviour.

= Why doesn't it work with my theme? =

Most likely, this is because your theme doesn't support the WordPress `custom-background` theme feature.
This plugin requires that your theme utilize this theme feature to work properly.
Unfortunately, there's just no reliable way for the plugin to overwrite the background if the theme doesn't support this feature.
You'll need to check with your theme author to see if they'll add support or switch to a different theme.

= My theme supports 'custom-background' but it doesn't work! =

That's unlikely.
Just to make sure, check with your theme author and make sure that they support the WordPress `custom-background` theme feature.
It can't be something custom your theme author created.  It must be the WordPress feature.

Assuming your theme does support `custom-background` and this plugin still isn't working, your theme is most likely implementing the custom background feature incorrectly.  However, I'll be more than happy to take a look.

= How do I add support for this in a theme? =

Your theme must support the [Custom Backgrounds](http://codex.wordpress.org/Custom_Backgrounds) feature for this plugin to work.

If you're a theme author, consider adding support for this if you can make it fit in with your design.  The following is the basic code, but check out the above link.

	add_theme_support( 'custom-background' );

= Are there any known limitations? =

This is not really a limitation of functionality, but since the background image container wraps the body element, it usually resembles the viewport dimensions. This means, that on themes where the navigation bar is on the side, the sidebar covers a part of the viewport and thus also a part of the image (logic, but noteworthy).

= Can you help me? =

Yes. I have a look at the plugin's support page two or three times a week and I provide some basic support there.

= Are there any known issues? =

Yes. If you're using the "Vantage" theme from SiteOrigin, the background image won't be displayed on Firefox. On every other browser it will. I'm on it.

== Screenshots ==

1. Multiple background views of a single post.
2. Custom background meta box on the edit post screen.
3. Custom background meta box.

== Changelog ==

= Version 0.2.6 =

1. The scripts for the frontend load only if needed

= Version 0.2.5 =

1. Added support for the blog page
2. Fixed support for single product page views
3. The "preserve scrolling" option superseeds the "Nicescrollr" plugin settings on the frontend, if both plugins are enabled
4. Code cleanup and some minor refactorings

= Version 0.2.4 =

1. Optimized the script for the public part
2. Added a section to the readme file regarding known issues
3. Updated the readme file

= Version 0.2.3 =

1. Fixed some bugs.
2. Added a background color to the image container to kind of simulate a "color" for the overlay.
3. Slightly enhanced meta box display behaviour.
4. Added support for "portfolio" post type / entries for web- and media workers :)

= Version 0.2.2 =

1. Removed display errors.

= Version 0.2.1 =

1. Resolved the translation bugs
2. Optimized the scrolling behaviour
3. Corrected the scroll ratio calculation
4. Corrected the "static" background image display
5. Corrected the meta box display behaviour
6. Added the option to preserve the nice scrolling behaviour without the need to use the parallax feature ( see "Settings / General / cbParallax" ).

= Version 0.2.0 =

1. Optimized the script responsible for the parallax effect
2. Added Nicescroll for smooth cross-browser scrolling

= Version 0.1.1 =

1. Massively refactored the script responsible for the parallax effect
2. Added the possibility to scroll the background image horizontally
3. Added a function to reposition the image on window resize automaticly
4. Improoved performance
5. Improoved compatibility with webkit, opera and ie browsers
6. Implemented a function that eases mousescroll

= Version 0.1.0 =

First release :-)
