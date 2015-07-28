=== cbParallax ===
px and a height of at least
Tags: post, posts, admin, image, images, background, color, Hintergrund, Bild, Hintergrundbild, parallax
Requires at least: 3.8
Tested up to: 4.2.3
Stable tag: 0.1.0
Contributors: OneMoreNerd
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows users to create a custom background with an optional vertical parallax effect on a per-post basis.


== Description ==

A plugin for allowing users to set a custom background with an optional verticalparallax effect on a per-post basis.  This plugin hooks into the WordPress `custom-background` theme feature and overwrites the values on single post views if the post has been given a custom background.

### Features ###

This plugin creates a custom meta box on the edit post screen.  From that point, you can select a custom color and/or image, and you can set the parallax effect as long as the image size allows it.  If you select an image, you'll be presented with additional options for how the image appears on the site. The image should at least have a width of 1920px and a minimum height of 1200px for the parallax feature to work.

This plugin is already translated into the German language, and a *.pot file for further localisations is also included.

### Requirements ###

Your theme must support the core WordPress implementation of the [Custom Backgrounds](http://codex.wordpress.org/Custom_Backgrounds) theme feature.

== Installation ==

1. Upload the `cb-parallax` folder to your `/wp-content/plugins/` directory.
2. Activate the "cbParallax" plugin through the "Plugins" menu in WordPress.
3. Edit a post to add a custom background.

== Frequently Asked Questions ==

### Why doesn't it work with my theme? ###

Most likely, this is because your theme doesn't support the WordPress `custom-background` theme feature.  This plugin requires that your theme utilize this theme feature to work properly.  Unfortunately, there's just no reliable way for the plugin to overwrite the background if the theme doesn't support this feature.  You'll need to check with your theme author to see if they'll add support or switch to a different theme.

### My theme supports 'custom-background' but it doesn't work! ###

That's unlikely.  Just to make sure, check with your theme author and make sure that they support the WordPress `custom-background` theme feature.  It can't be something custom your theme author created.  It must be the WordPress feature.

Assuming your theme does support `custom-background` and this plugin still isn't working, your theme is most likely implementing the custom background feature incorrectly.  However, I'll be more than happy to take a look.

### How do I add support for this in a theme? ###

Your theme must support the [Custom Backgrounds](http://codex.wordpress.org/Custom_Backgrounds) feature for this plugin to work.

If you're a theme author, consider adding support for this if you can make it fit in with your design.  The following is the basic code, but check out the above link.

	add_theme_support( 'custom-background' );

### Can other users on my site add backgrounds? ###

Some sites have multiple writers/authors who write posts.  However, since custom backgrounds tend to be a design-related option, only administrators have access to altering backgrounds in a default WordPress install.  There is a way around this, which is to give permission by assigning a capability to user roles.

In order to manage capabilities and roles, you need a plugin like [Members](http://wordpress.org/plugins/members), which is a plugin Justin Tadlock created for managing sites with multiple users.  It's something you should be using for any site with multiple levels of users (i.e., all users are not admins).  This plugin will allow you to add or create new capabilities for any role.

The capability required for being able to add per-post backgrounds is one of the following:

* `cb_parallax_edit` - The user can edit backgrounds on posts they have written.
* `edit_theme_options` - The user can edit all WordPress theme options (**not** recommended for anyone other than administrators).

Using the Members plugin, you can assign one of the above capabilities to allow other, non-administrator users to edit backgrounds for their posts.

Also, a user must have the `upload_files` capability to upload new images, but this is a WordPress thing and not specific to the plugin.

### Does it support custom post types? ###

The plugin supports WordPress posts and pages out of the box.

Because it's impossible for me to accurately determine what a **custom** post type should do, I've left it up to those of you actually building custom post type plugins to support this.  If you'd like to allow custom backgrounds on singular views of your post type, add `'custom-background'` to your post type `supports` array during registration.  Obviously, your post type would need to be publicly queryable and display something on the front end for single post views. 

Or, if you have a plugin with post types that you'd like for me to add support for, let me know.  I'll be more than happy to add the support via this plugin.

### Can you help me? ###

Unfortunately, I cannot provide free support for this plugin. I honestly wish I could. However, very soon you can go <a href="https://github.com/demispatti/cb-parallax" target="_blank">here</a> for further informations.

== Screenshots ==

1. Multiple background views of a single post.
2. Custom background meta box on the edit post screen.
3. Custom background meta box.

== Changelog ==

### Version 0.1.0 ###

* First release :-)
