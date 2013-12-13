Initializr+ for WordPress is a starter WordPress theme
======================================================

Initializr+ for WordPress is a starter WordPress theme of the HTML5 Responsive boilerplate project from http://www.initializr.com/

The plus sign means that is it not just the static files converted to a theme but it also contains the following features without plugins already built into functions.php

The file iplus.php can be copied and included as is in any theme folder (including the folders IDS, Twig) so that any theme can immediately benefit from the following additions.

FEATURES of iplus.php file which can be immediately included in any theme folder
--------------------------------------------------------------------------------

- a simple breadcrumb implementation
- includes a thumbnail generator based on phpThumb and function to generate arbitrary thumbnails of any size from the original images
- ajax loading of more posts (example in tpl_blog.php)
- lazy loading of images (the images are loaded when the user scrolls to that portion of the page). Example in tpl_blog.php
- adds a Last Login date and time to the admin bar in My Account menu
- output seo meta data such as title,keywords and description for each page that defines any of the meta_title, meta_keywords, meta_description custom fields.
- refined search procedure to perform full text search in title,excerpt,content and also search in posts that include the search term in category names, tags and the special meta_keywords custom field
- integrated Twig template engine. A simple way to use Twig to parse a template. Just rename the template (e.g. page.twig) and use twig syntax. All WordPress functions and globals are available in the twig template to use. (example in page.twig)
- pagination function to work with custom WP_Query (example in tpl_blog.php)
- phpIDS integrated : the php Intrusion Detection System from http://www.phpids.org/
- log menu in admin to capture and log errors, searches and intrusion attempts
- simple contact form temlate page
- contains a Blog template (tpl_blog.php) which you can assign to any page to display all your posts as a list
- Generate custom style nested comments

More Information
----------------

Visit the official website for more information.

[1]: http://www.archteon.com/

