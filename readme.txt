=== stripShow ===
Contributors: monkeyangst
Donate link: http://stripshow.monkeylaw.org/
Tags: webcomics, plugin, comics, cartoons, art, automation, strips
Requires at least: 2.8
Tested up to: 3.5
Stable tag: 2.5.4

stripShow is a plugin that turns WordPress into a full-featured webcomics automation system.


== Description ==

So you've decided to take the plunge and enter the wide, wild world of webcomics. You've painstakingly crafted a few exquisitely-rendered comic pages or strips, or perhaps you just dashed off a few stick figures. Whatever. It doesn't matter. The world of webcomics has room for all types. But now you have to get your strips online. How?

All the savvier webcomics creators these days are using WordPress, you've heard. You may have even heard of one or two add-on packages out there that seem designed specifically for webcomics. Some are themes... you download a fully-designed theme and alter it to suit your needs. Others are plugins... you roll your own theme from the ground up using PHP code to put in the necessary comics-oriented features. All in all, it's a little daunting.

That's why you should consider stripShow. stripShow is the all-in-one webcomics package for WordPress. Both a plugin and a powerful theme framework, stripShow is the only webcomics software you'll need. From uploading your strips to crafting a beautiful theme, stripShow gets the drudgery of webcomics archiving out of the way and frees you to focus on what matters... your comics!

stripShow is designed for webcomics creators at all levels:

For the Beginner:

Say you don't want to spend a lot of time creating a theme. Say you've already got a WordPress theme you like. stripShow's AutoComic feature can turn that theme into a webcomics presentation platform, all without editing a single line of code.

AutoComic places your comic front and center on your index page, and can even fill out your archives with thumbnails of past comics. Navigation buttons are available in a variety of attractive styles to get you started. AutoComic even includes a widget-ready "sidebar" of its very own, allowing you to start using stripShow's comics widgets today.

AutoComic can be your first step to getting your comic on the web. Get your comics online and in front of readers ''now'', not a month from now. Spend your time doing what you set out to do -- drawing comics! (And, of course, uploading them using stripShow's elegant, WordPress-integrated management system.)

Eventually, of course, you'll want your webcomic site to be a little more ''yours''. You'll want to start making your own theme. stripShow has you covered there, too...

For the Expert:

stripShow isn't just a plugin. It comes with a powerful theme framework called stripShow Sandbox. Think of this framework like a skeleton, on which to hang your webcomics-oriented site design. Create your own customized child theme around this skeleton, confident that future upgrades will not wipe out your painstaking work. 

Under the hood, stripShow Sandbox can be customized many ways. You can choose a page layout, choosing from one or two sidebars, located on the left, right, or either side of your content. But even beyond that, stripShow Sandbox's action hooks make it possible to completely change your design around. Move the comic to a different location, add and remove elements, all without changing the original code.

Or perhaps you really do want something completely different. stripShow includes dozens of template tags which can be put in any theme of your choosing -- or creation -- to allow you complete control over the way your site behaves.

Beyond the way your site looks, there's the matter of your comics themselves. As your archive grows, readers are going to want to be able to find strips quickly. stripShow provides a number of ways to organize and search your archives. Storylines break your archive into chapters. Character tags keep track of who stars in which strip. Built-in transcripts let you put a complete account of a comic's action, dialogue, or whatever else you want, right there in each post, where search engines can find it.
== Installation ==

Installation of stripShow is easy. First, you need a working WordPress installation.

1. From the Plugins menu, select Add New.
2. Search for stripShow.
3. Install the plugin.

That's it! stripShow 2.5 automatically does the following on installation:

*	If you haven't yet created a category to hold comics, creates one for you.
*	Creates a symbolic link in your `themes` folder to stripShow Sandbox. This will make it easy to upgrade stripShow in the future without worrying about anything being out of date.
*	Copies a bare-bones child theme into your `themes` folder so you can get started customizing right away.
*	Creates folders to hold your comics in `wp-content`.


== Frequently Asked Questions ==

= Can I use one of Tyler Martin's ComicPress themes with stripShow? =

Not as-is, no. While I had hoped for full compatibility with ComicPress in 2.0, new functions added to ComicPress conflict with functions already existing in stripShow. To use stripShow with ComicPress would require the editing of ComicPress's functions.php file.

== Screenshots ==
1. The stripShow Add Comics interface.
2. The stripShow Options interface.
3. The stripShow Storylines interface.


== Changelog ==
= 2.5.4 = 
	Fixed local server reference in admin.css.
= 2.5.3 = 
	Fixed a vulnerability by switching to WP-native redirects.
= 2.5.2 =
*	Added the ability to put navigation buttons above the comic, below the comic, or both, in AutoComic.
= 2.5.1 = 
*	Fixed a bug wherein tooltips in AutoComic couldn't handle non-Western characters. Now the non-English-speaking world can use AutoComic