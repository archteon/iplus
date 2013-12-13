<?php

include('iplus.php');

function iplus_setup() {

    /*
     * Makes the theme available for translation.
     *
     * Translations can be added to the /languages/ directory.
     * If you're building a theme based on Twenty Thirteen, use a find and
     * replace to change 'twentythirteen' to the name of your theme in all
     * template files.
     */

    if ($_SERVER['REQUEST_URI'] == '/something') {
        //do work upon calling the specified uri which should return a 404 error
    }

    load_theme_textdomain('iplus', get_template_directory() . '/languages');

    // Adds RSS feed links to <head> for posts and comments.
    add_theme_support('automatic-feed-links');

    // Switches default core markup for search form, comment form, and comments to output valid HTML5.
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list'));

    add_theme_support('menus');

    // This theme uses wp_nav_menu() in one location.
    register_nav_menu('primary', __('Top Navigation Menu', 'iplus'));

    /*
     * This theme uses a custom image size for featured images, displayed on "standard" posts and pages.
     */
    add_theme_support('post-thumbnails');
}

add_action('after_setup_theme', 'iplus_setup');


/**
 * Enqueue scripts and styles
 */
function liketheme_enqueue_scripts() {

    $ver_css = '1.0.0';
    $ver_js = '1.0.0';

    if (is_single() && comments_open() && (get_option('thread_comments') == 1)) {
        //wp_enqueue_script('comment-reply'); //wp_default_scripts
        if (get_option('comment_registration')) {
            if (is_user_logged_in())
                wp_enqueue_script('comment-reply-script', includes_url('js/comment-reply.min.js'), array(), $ver_js, true);
        } else
            wp_enqueue_script('comment-reply-script', includes_url('js/comment-reply.min.js'), array(), $ver_js, true);
    }


}

add_action('wp_enqueue_scripts', 'liketheme_enqueue_scripts');

function iplus_widgets_init() {

    register_sidebar(array(
        'name' => __('Sidebar', 'iplus'),
        'id' => 'sidebar-1',
        'description' => __('Appears on posts and pages in the sidebar.', 'iplus'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

}
add_action('widgets_init', 'iplus_widgets_init');

function iplus_reset_permalinks() {
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure( '/%category%/%postname%/' );
}
add_action( 'init', 'iplus_reset_permalinks' );

function iplus_template_select() {
    
    if (is_search() && !empty($_GET['s'])) {
        wp_redirect(home_url("/search/") . urlencode(get_query_var('s')));
        exit();
    }
}
add_action('template_redirect', 'iplus_template_select');


function iplus_404_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('404.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( '404_template', 'iplus_404_template',  99 );

function iplus_archive_template( $template ) {        
        if($template!='')
            echo $template;
        elseif( ($template = locate_template('archive.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'archive_template', 'iplus_archive_template',  99 );

function iplus_index_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('index.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'index_template', 'iplus_index_template',  99 );


function iplus_category_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('category.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'category_template', 'iplus_category_template',  99 );


function iplus_author_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('author.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'author_template', 'iplus_author_template',  99 );



function iplus_tag_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('tag.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'tag_template', 'iplus_tag_template',  99 );


function iplus_home_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('home.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'home_template', 'iplus_home_template',  99 );



function iplus_front_page_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('front-page.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'front_page_template', 'iplus_front_page_template',  99 );


function iplus_page_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('page.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'page_template', 'iplus_page_template',  99 );


function iplus_taxonomy_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('taxonomy.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'taxonomy_template', 'iplus_taxonomy_template',  99 );


function iplus_search_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('search.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'search_template', 'iplus_search_template',  99 );


function iplus_attachment_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('attachment.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'attachment_template', 'iplus_attachment_template',  99 );


function iplus_single_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('single.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'single_template', 'iplus_single_template',  99 );


function iplus_date_template( $template ) {        
        if($template!='')
            return $template;
        elseif( ($template = locate_template('date.twig')) != '' ){
            return $template;
        }        
	return $template;
}
add_filter( 'date_template', 'iplus_date_template',  99 );


function iplus_template_include( $template ) {
    
        $pinfo = pathinfo($template);
    
        if($pinfo['extension']=='twig'){
                
            require_once get_template_directory().'/Twig/Autoloader.php';
            Twig_Autoloader::register();

            $loader = new Twig_Loader_Filesystem(get_template_directory());            
            
            if(WP_DEBUG){
                $twig = new Twig_Environment($loader, array( 'debug' => true ) );                 
                $twig->addExtension(new Twig_Extension_Debug());
            }
            else
                $twig = new Twig_Environment($loader);
            
            do_action('twig_loaded',$twig);
            
            //We use a filter to give the oportunity to completely replace functions.
            //False will completely skip function loading. Maybe the user wants to load his own functions in 'twig_loaded' action
            //add_filter( 'twig_load_user_functions', '__return_false' );
            $funcs = get_defined_functions();
            if( ($funcs = apply_filters('twig_load_user_functions',$funcs['user'])) !== false ){                                    
                    if( is_array($funcs) && !empty ($funcs)){
                        foreach($funcs as $idx=>$value){                                                
                            $function = new Twig_SimpleFunction($value, $value );
                            $twig->addFunction($function);
                        }                                         
                    }
            }
            
            //Filter variables to be made available to twig. By default all GLOBALS variables are added.
            //To completely skip loading of variables
            //add_filter( 'twig_load_globals', '__return_false' );
            $globs = $GLOBALS;
            if( ($globs = apply_filters('twig_load_globals',$globs)) === false ){                                    
                    $globs=array();
            }

            //Also make all globals variables available to twig
            echo $twig->render($pinfo['basename'], $globs);
            
            return false;//do not include the template
            
        }
        else
            return $template;
        

}
add_filter( 'template_include', 'iplus_template_include',  99 );

?>