=== Wiki Append ===
Contributors: oltdev
Tags: mediawiki, wiki, append, WikiInc, include
Requires at least: 2.5
Tested up to: 2.7.1
Stable tag: `/trunk/`

Append a mediawiki page at the end of the regular wordpress page.

== Description ==

This plugin enables page or post authors to scrape content from mediawiki pages and appending it to their pages. 
It works by scraping content from mediawiki pages by going to a special mediawiki page url.
 
For example http://en.wikipedia.org/wiki/Wordpress?action=render

The content rendered on the final page or post will always be the newest content, however it is not searchable via regular WordPress search form. 

Use case:
This plugin is great for adding documentation to your site. 
Since wordpress.org is using mediawiki for the codex you could potentially display wordpress codex pages on your site without much effort, just enter the full url of the specific wordpress codex page. 

Enjoy  


== Installation ==
 
1. Upload wiki-append.zip to the /wp-content/plugins/
2. Unzip into its own folder /wp-content/plugins/
3. Activate the plugin through the 'Plugins' menu in WordPress by clicking "Wiki Append"


== Frequently Asked Questions ==

= Images are not displaying properly =

There is an error with your mediawiki install. 
Try make sure that the images are absolutely linked. which means that they are starting with http:// 

= How do I make the edit links disappear ? =
Try editing your css 
and adding something like

	#wiki-append-content h2 span.endsection {
	display:none;
	}

== Screenshots ==

1. Append Wiki on the post/page dashboard.



