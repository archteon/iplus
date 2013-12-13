<?php

/*
Theme Name: Initializr Plus
Description: WordPress Theme for the Initiliazr HTML5 Responsive Template (http://www.initializr.com) with some useful additions
Version: 1.0
Author: Constantine, Originally initialized by Jonathan Verrecchia
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: initializr, http://www.archteon.com/
I do not clain copyright to all of the code below.
*/

/*
You may include this file in functions.php in your onw theme icnluding the folders Twig,IDS to get all functionality as described below
- adds a Last Login date and time to the admin bar in My Account menu
- output seo meta data such as title,keywords and description for each page that defines any of the meta_title, meta_keywords, meta_description custom fields.
- refined search procedure to perform full text search in title,excerpt,content and also search in posts that include the search term in category names, tags and the special meta_keywords custom field
- pagination function to work with custom WP_Query queries (example in tpl_blog.php)
- simple phpIDS integration : the php Intrusion Detection System from http://www.phpids.org/
- log menu in admin to capture and log errors, searches, intrusion attempts or custom messages through ErrorHandler::LogMsg(title,message)
*/

//----------------BEGIN LOGGER ----------------------------------------//

define('IS_WARNING_FATAL', true);
// The error types to be reported
define('ERROR_TYPES', E_ALL);
// Settings about mailing the error messages to admin
define('SEND_ERROR_MAIL', false);
define('ADMIN_ERROR_MAIL', get_option( 'admin_email', '' ));
define('SENDMAIL_FROM', get_option( 'admin_email', '' ));
//ini_set('sendmail_from', SENDMAIL_FROM);
define('INSERT_POST_ERRORS', true); //Insert errors as posts
// By default we don't log errors to a file
define('LOG_ERRORS', true);
define('LOG_ERRORS_FILE', 'C:\\wamp\\www\\iplus.mwa\errors.log.txt'); // On Windows
// define('LOG_ERRORS_FILE', '/home/username/mydomain.com.errors.log'); // On Linux
/* Generic error message to be displayed instead of debug info (when DEBUGGING is false) */
define('SITE_GENERIC_ERROR_MESSAGE', '<h1>Error!</h1>');

class ErrorHandler {

    private function __construct() {
        // Private constructor to prevent direct creation of object
    }

    /* Set user error-handler method to ErrorHandler::Handler method */
    public static function SetHandler($errTypes = ERROR_TYPES) {
        return set_error_handler(array('ErrorHandler', 'Handler'), $errTypes);
    }

    public static function LogMsg($title, $msg=''){
        if(INSERT_POST_ERRORS==true)
            wp_insert_post(array('post_type'=>'error_log', 'post_status'=>'private', 'post_excerpt'=>addslashes($msg),'post_title'=> addslashes($title) ));
        if (LOG_ERRORS == true)
            error_log($title." at ".date('F j, Y, g:i a')."\r\n".$msg, 3, LOG_ERRORS_FILE);
    }

    // Error handler method
    public static function Handler($errNo, $errStr, $errFile='', $errLine=0) {
        $backtrace = ErrorHandler::GetBacktrace(2);
        $error_message = "\r\nERRNO: $errNo\r\nTEXT: $errStr" . "\r\nLOCATION: $errFile, line " . "$errLine, at " . date('F j, Y, g:i a') . "\r\nShowing backtrace:\r\n$backtrace\r\n\r\n";
        if (SEND_ERROR_MAIL == true)
            error_log($error_message, 1, ADMIN_ERROR_MAIL, "From: " . SENDMAIL_FROM . "\r\nTo: " . ADMIN_ERROR_MAIL);
        if (LOG_ERRORS == true)
            error_log($error_message, 3, LOG_ERRORS_FILE);
        if (($errNo == E_WARNING && IS_WARNING_FATAL == false) || ($errNo == E_NOTICE || $errNo == E_USER_NOTICE)) {
            if (WP_DEBUG == true){
                if(INSERT_POST_ERRORS==true)
                    wp_insert_post(array('post_type'=>'error_log', 'post_status'=>'private', 'post_excerpt'=>addslashes($error_message),'post_title'=> addslashes( $errStr. " LOCATION: $errFile, line " . "$errLine")));
            }
        }
        else {
            if (WP_DEBUG == true){
                if(INSERT_POST_ERRORS==true)
                    wp_insert_post(array('post_type'=>'error_log', 'post_status'=>'private', 'post_excerpt'=>addslashes($error_message),'post_title'=> addslashes($errStr. " LOCATION: $errFile, line " . "$errLine") ));
            }
            else{
                if(INSERT_POST_ERRORS==true)
                    wp_insert_post(array('post_type'=>'error_log', 'post_status'=>'private', 'post_excerpt'=>addslashes($error_message),'post_title'=>  addslashes( $errStr. " LOCATION: $errFile, line " . "$errLine")));
            }
        }
    }

    // Builds backtrace message
    public static function GetBacktrace($irrelevantFirstEntries) {
        $s = '';
        $MAXSTRLEN = 64;
        $trace_array = debug_backtrace();
        for ($i = 0; $i < $irrelevantFirstEntries; $i++)
            array_shift($trace_array);
        $tabs = sizeof($trace_array) - 1;
        foreach ($trace_array as $arr) {
            $tabs -= 1;
            if (isset($arr['class']))
                $s .= $arr['class'] . '.';
            $args = array();
            if (!empty($arr['args']))
                foreach ($arr['args']as $v) {
                    if (is_null($v))
                        $args[] = 'null';
                    elseif (is_array($v))
                        $args[] = 'Array[' . sizeof($v) . ']';
                    elseif (is_object($v))
                        $args[] = 'Object: ' . get_class($v);
                    elseif (is_bool($v))
                        $args[] = $v ? 'true' : 'false';
                    else {
                        $v = (string) @$v;
                        $str = htmlspecialchars(substr($v, 0, $MAXSTRLEN));
                        if (strlen($v) > $MAXSTRLEN)
                            $str .= '...';
                        $args[] = '"' . $str . '"';
                    }
                }
            $s .= $arr['function'] . '(' . implode(', ', $args) . ')';
            $line = (isset($arr['line']) ? $arr['line'] : 'unknown');
            $file = (isset($arr['file']) ? $arr['file'] : 'unknown');
            $s .= sprintf(' # line %4d, file: %s', $line, $file);
            $s .= "\r\n";
        }
        return $s;
    }

}

function custom_error_handler_init(){
    ErrorHandler::SetHandler();
}
add_action('init','custom_error_handler_init', 2);  //Execute this as early as possible but after the Error Log Custom post type has been registered (see below)

function custom_excerpt_textarea_height() {
    $screen = get_current_screen();
    if($screen->post_type=='error_log' || $screen->post_type=='ids_intrusions' ){
        echo'
        <style type="text/css">
            #excerpt{ height:250px; }
        </style>
        ';
    }
}
add_action('admin_head', 'custom_excerpt_textarea_height');

function custom_error_log_bubble( $menu ) {

    $count_posts = wp_count_posts('error_log','readable');
    $error_count = $count_posts->private;

    $count_posts = wp_count_posts('search_queries','readable');
    $search_count = $count_posts->private;

    if($error_count>0 || $search_count>0){
        foreach( $menu as $menu_key => $menu_data ) {
            if( 'logs' != $menu_data[2] )
                continue;
            else{
                $s = ($error_count>0)?'<span style="color:red;display:inline;">'.number_format_i18n($error_count).' er</span>, ':'';
                $s .= ($search_count>0)?number_format_i18n($search_count).' s':'';
                $menu[$menu_key][0] .= " <span class='update-plugins'><span class='plugin-count'>".$s.'</span></span>';
                break;
            }
        }
    }

    return $menu;
}
add_filter( 'add_menu_classes', 'custom_error_log_bubble');

function error_log_register_post_type() {

    //$error_count = $search_count = $intrusion_count = 0;

    register_post_type( 'error_log',array(

            'label'  => 'Error Log',
            'public' => false,
            'capabilities' => array(
                'publish_posts' => 'activate_plugins',
                'edit_posts' => 'activate_plugins',
                'edit_others_posts' => 'activate_plugins',
                'delete_posts' => 'activate_plugins',
                'delete_others_posts' => 'activate_plugins',
                'read_private_posts' => 'activate_plugins',
                'edit_post' => 'activate_plugins',
                'delete_post' => 'activate_plugins',
                'read_post' => 'activate_plugins'
            ),
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => 'logs',
            'show_in_admin_bar' => false,
            'rewrite' => false,
            'supports' => array('title','excerpt'),
            /*'menu_position' => 100*/

        )
    );

    register_post_type( 'search_queries',array(

            'label'  => 'Search Queries',
            'public' => false,
            'capabilities' => array(
                'publish_posts' => 'activate_plugins',
                'edit_posts' => 'activate_plugins',
                'edit_others_posts' => 'activate_plugins',
                'delete_posts' => 'activate_plugins',
                'delete_others_posts' => 'activate_plugins',
                'read_private_posts' => 'activate_plugins',
                'edit_post' => 'activate_plugins',
                'delete_post' => 'activate_plugins',
                'read_post' => 'activate_plugins'
            ),
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => 'logs',
            'show_in_admin_bar' => false,
            'rewrite' => false,
            'supports' => array('title','excerpt'),
            /*'menu_position' => 100*/

        )
    );

    register_post_type( 'ids_intrusions',array(

            'label'  => 'IDS intrusions',
            'public' => false,
            'capabilities' => array(
                'publish_posts' => 'activate_plugins',
                'edit_posts' => 'activate_plugins',
                'edit_others_posts' => 'activate_plugins',
                'delete_posts' => 'activate_plugins',
                'delete_others_posts' => 'activate_plugins',
                'read_private_posts' => 'activate_plugins',
                'edit_post' => 'activate_plugins',
                'delete_post' => 'activate_plugins',
                'read_post' => 'activate_plugins'
            ),
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => 'logs',
            'show_in_admin_bar' => false,
            'rewrite' => false,
            'supports' => array('title','excerpt'),
            /*'menu_position' => 100*/

        )
    );


}
add_action('init', 'error_log_register_post_type', 1 );  //Execute this as early as possible

function error_log_admin_menu(){

    global $submenu;

    add_menu_page( 'Logs', 'Logs', 'manage_options', 'logs', null, '', 101 );

    if(isset($submenu['logs'])){

        $count_posts = wp_count_posts('error_log','readable');
        $error_count = $count_posts->private;

        $count_posts = wp_count_posts('search_queries','readable');
        $search_count = $count_posts->private;

        $count_posts = wp_count_posts('ids_intrusions','readable');
        $intrusion_count = $count_posts->private;

        foreach($submenu['logs'] as $index => $log){
            if($log[2]=='edit.php?post_type=error_log')
                $submenu['logs'][$index][0] = $log[3]. ' ('.$error_count.')';
            elseif($log[2]=='edit.php?post_type=search_queries')
                $submenu['logs'][$index][0] = $log[3].' ('.$search_count.')';
            elseif($log[2]=='edit.php?post_type=ids_intrusions')
                $submenu['logs'][$index][0] = $log[3]. ' ('.$intrusion_count.')';
        }

    }


}
add_action('admin_menu', 'error_log_admin_menu');

//----------------END LOGGER ----------------------------------------//






//----------------BEGIN IDS (Intrusion Detection System from http://www.phpids.org) Integration ----------------//


function iplus_initialize() {

    if (!is_admin()) {

        set_include_path(get_include_path() . PATH_SEPARATOR . get_template_directory() . '/');

        define('BASEDIR', get_template_directory() . '/');
        require_once BASEDIR . 'IDS/Init.php';
        try {


            /*
              $request = array(
              'REQUEST' => $_REQUEST,
              'GET' => $_GET,
              'POST' => $_POST,
              'COOKIE' => $_COOKIE
              );
             */

            //$request = $_GET;

            $request = array(
                'GET' => $_GET,
                'POST' => $_POST,
                'COOKIE' => $_COOKIE
            );

            $init = IDS_Init::init(BASEDIR . 'IDS/Config/Config.ini.php');

            $init->config['Logging']['path'] = BASEDIR . 'IDS/tmp/idslog.txt';
            $init->config['General']['tmp_path'] = BASEDIR . 'IDS/tmp';
            $init->config['General']['filter_path'] = BASEDIR . 'IDS/rules1.xml';
            $init->config['Caching']['caching'] = 'none';
            $init->config['General']['exceptions'][] = 'COOKIE.__utmz';
            if (is_user_logged_in() && current_user_can('edit_theme_options'))
                $init->config['General']['exceptions'][] = 'POST.customized';    //Exclude checks when previewing a theme

            $ids = new IDS_Monitor($request, $init);
            $result = $ids->run();

            if (!$result->isEmpty()) {
                $out = '';
                $title = date_i18n(get_option('date_format') . ' ' . get_option('time_format'), current_time('timestamp')) . ' TOTAL IMPACT: ' . $result->getImpact() . ' in file: ' . $_SERVER['PHP_SELF'];
                $out .= '<h4>' . $title . '</h4>';
                $out .= "<hr>\n\r";
                $out .= '<pre>' . $result . '</pre>' . "\n\r";

                wp_insert_post(array('post_type' => 'ids_intrusions', 'post_status' => 'private', 'post_excerpt' => strip_tags($out), 'post_title' => $title));

                $f = @fopen(BASEDIR . 'IDS/tmp/report.htm', 'a');
                @fputs($f, $out);
                @fclose($f);
                header("Location: /404", true, 404);
                exit();
            }
        } catch (Exception $e) {
            $f = @fopen(BASEDIR . 'IDS/tmp/report.htm', 'a');
            @fputs($f, '<pre>An error occured:' . $e->getMessage() . '</pre>');
            @fclose($f);
        }

    }

    add_rewrite_rule('thumb.php', 'wp-content/themes/' . get_template() . '/phpthumb/phpThumb.php', 'top');
}
add_action('init', 'iplus_initialize');


//----------------END IDS (Intrusion Detection System from http://www.phpids.org) Integration ----------------//



//----------------BEGIN META DATA OUTPUT TO HEADER -------------------------------------//
/*
Output SEO meta data such as title,keywords and description for each page that defines any of the meta_title, meta_keywords, meta_description custom fields.
Define these custom fields for any post. They will be printed in the header of the post or page
meta_title
meta_description
meta_keywords
*/

function iplus_header() {
?>

    <?php if (is_home() || is_front_page()): ?>

        <!-- standard meta tags for homepage -->
        <meta name="description" content="">
        <meta name="keywords" content="">

    <?php else: ?>

        <?php
        global $wp_query;

        if (isset($wp_query->queried_object_id) && $meta_keywords = get_post_meta($wp_query->queried_object_id, 'meta_keywords', true)) {
            echo '<meta name="keywords" content="' . esc_attr($meta_keywords) . '" />' . "\r\n";
        } elseif (isset($wp_query->queried_object_id) && ( is_single() || is_page() )) {
            $the_terms = get_the_tags($wp_query->queried_object_id);
            $the_cats = get_the_category($wp_query->queried_object_id); //will return empty array on failure

            if (is_array($the_terms) && !is_wp_error($the_terms) && (!empty($the_terms) || !empty($the_cats) )) {
                if (is_array($the_cats) && !is_wp_error($the_cats))
                    $the_terms = array_merge($the_terms, $the_cats);
                $keywords = array();
                $keywords[] = single_post_title('', false);
                foreach ($the_terms as $the_term) {
                    $keywords[] = strtolower($the_term->name);
                }
                $keywords = array_unique($keywords);
                $keywords = implode(', ', $keywords);
                echo '<meta name="keywords" content="' . esc_attr($keywords) . '" />' . "\r\n";
            }
        } elseif (is_tag() || is_category()) {
            echo '<meta name="keywords" content="' . esc_attr(single_term_title('', false)) . '" />' . "\r\n";
        } else {
            echo '<meta name="keywords" content="default keywords">';
        }

        if (isset($wp_query->queried_object_id) && $meta_description = get_post_meta($wp_query->queried_object_id, 'meta_description', true)) {
            $meta_description = wp_strip_all_tags($meta_description, true);
            $meta_description = preg_replace('/\s+/', ' ', trim($meta_description));
            echo '<meta name="description" content="' . esc_attr($meta_description) . '" />' . "\r\n";
        } elseif (isset($wp_query->queried_object_id) && (is_tag() || is_category())) {
            $term_description = term_description($wp_query->queried_object_id, (is_tag() ? 'post_tag' : 'category'));
            if (empty($term_description))
                echo '<meta name="description" content="' . esc_attr(get_option('blogdescription')) . '" />' . "\r\n";
            else {
                $term_description = wp_strip_all_tags($term_description, true);
                $term_description = preg_replace('/\s+/', ' ', trim($term_description));
                echo '<meta name="description" content="' . esc_attr($term_description) . '" />' . "\r\n";
            }
        } elseif (isset($wp_query->queried_object_id) && ( is_single() || is_page() )) {

            $desc = single_post_title('Information about ', false) . '. ';

            $the_post = get_post($wp_query->queried_object_id);
            $the_excerpt = $the_post->post_excerpt ? $the_post->post_excerpt : $the_post->post_content; //Gets post_excerpt or post_content
            $the_excerpt = wp_strip_all_tags(strip_shortcodes($the_excerpt), true);
            $words = explode(' ', $the_excerpt, 35);
            array_pop($words);

            $desc .= implode(' ', $words);

            $desc = wp_strip_all_tags($desc, true);
            $desc = preg_replace('/\s+/', ' ', trim($desc));

            //Optional:Add a custom meta_description field to avoid regenerate it
            //add_post_meta($wp_query->queried_object_id, 'meta_description', $desc, true);

            echo '<meta name="description" content="' . esc_attr($desc) . '" />' . "\r\n";
        } else {
            echo '<meta name="description" content="' . esc_attr(get_option('blogdescription')) . '" />' . "\r\n";
        }
        ?>


    <?php endif; ?>


<?php
}

add_action('wp_head', 'iplus_header');

//----------------END META DATA OUTPUT TO HEADER -------------------------------------//




//----------------BEGIN IMPROVED SEARCH ----------------------------------------------//

/*
This is a refined search to perform full text search in title,excerpt,content (do not forget to add a full text search index in your database. It will WORK without the index but without the benefit of added speed.)
It will also search the term in category names, tags and the special meta_keywords (comma separated) custom field (if it exists)
So this means that any tags you add to the post will bring up the post in the search results for these tags
*/


function iplus_custom_search_filter($s, $wp_query) {

    if (!is_admin() && $wp_query->is_search()) {
        global $wpdb;

        $the_query = trim(wp_strip_all_tags(get_search_query(false)));

        $term_set = str_replace(str_split("+-><,?/\[]{}%@#!~`$^&*()='"), "", $the_query); //Remove certain characters
        $term_set = preg_replace('/\s+/', ',', $term_set); //Replace whitespace with comma
        $terms = explode(',', $term_set);

        $term = esc_sql($the_query);

        $search = " AND ($wpdb->posts.post_type='post' ) ";
        $search .= ' AND ( ';
        $search .= " MATCH ($wpdb->posts.post_title, $wpdb->posts.post_content, $wpdb->posts.post_excerpt) AGAINST ('$term' IN BOOLEAN MODE) ";
        $search .= " OR ( ($wpdb->term_taxonomy.taxonomy = 'post_tag' OR $wpdb->term_taxonomy.taxonomy = 'category') AND $wpdb->terms.name LIKE '%" . esc_sql(like_escape($the_query)) . "%' ) ";
        $search .= " OR ( ($wpdb->term_taxonomy.taxonomy = 'post_tag' OR $wpdb->term_taxonomy.taxonomy = 'category') AND FIND_IN_SET($wpdb->terms.name,'" . esc_sql($term_set) . "')>0 ) ";
        $search .= " OR (  $wpdb->postmeta.meta_value REGEXP '" . esc_sql(implode('|', $terms)) . "' ) "; //search in meta_keywords field of single posts
        $search .= ' ) ';

        if (!is_user_logged_in())
            $search .= " AND ($wpdb->posts.post_password = '') ";

        return $search;
    }
    return $s;
}

add_filter('posts_search', 'iplus_custom_search_filter', 10, 2);

function iplus_custom_search_fields($fields, $wp_query) {
    if (!is_admin() && $wp_query->is_search()) {
        global $wpdb;
        $term = esc_sql(trim(get_search_query(false)));
        return $fields . ", MATCH ($wpdb->posts.post_title, $wpdb->posts.post_content, $wpdb->posts.post_excerpt) AGAINST ('$term' IN BOOLEAN MODE) as relevance ";
    } else
        return $fields;
}
add_filter('posts_fields', 'iplus_custom_search_fields', 10, 2);

function iplus_custom_orderby($orderby, $wp_query) {
    if (!is_admin() && $wp_query->is_search()) {
        global $wpdb;
        return " relevance DESC, $wpdb->posts.post_date DESC ";
    } else
        return $orderby;
}
add_filter('posts_orderby', 'iplus_custom_orderby', 10, 2);

function iplus_custom_join($join, $wp_query) {
    if (!is_admin() && $wp_query->is_search()) {
        global $wpdb;
        $join = " LEFT JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id) ";
        $join .= " LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) ";
        $join .= " LEFT JOIN $wpdb->terms ON ($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id) ";
        $join .= " LEFT JOIN $wpdb->postmeta ON ($wpdb->postmeta.post_id = $wpdb->posts.ID AND $wpdb->postmeta.meta_key='meta_keywords' ) ";
        return $join;
    } else
        return $join;
}
add_filter('posts_join', 'iplus_custom_join', 10, 2);

function iplus_custom_groupby($groupby, $wp_query) {
    if (!is_admin() && $wp_query->is_search()) {
        global $wpdb;
        return " $wpdb->posts.ID ";
    } else
        return $groupby;
}

add_filter('posts_groupby', 'iplus_custom_groupby', 10, 2);

//----------------END IMPROVED SEARCH ----------------------------------------------//




//---------------BEGIN LAST LOGIN TIME----------------------------------------------//
//Store last login date and time
function user_last_login_time($user_login, $user) {
    if (get_user_meta($user->ID, '_last_login_timestamp', true) == '')
        update_user_meta($user->ID, '_last_login_text', _('Unknown'));
    else
        update_user_meta($user->ID, '_last_login_text', date_i18n(get_option('date_format') . ' ' . get_option('time_format'), get_user_meta($user->ID, '_last_login_timestamp', true)));
    update_user_meta($user->ID, '_last_login_timestamp', current_time('timestamp'));
}
add_action('wp_login', 'user_last_login_time', 10, 2);


//Show last login date and time under my account in admin bar
function last_login_admin_bar($wp_admin_bar) {
    $my_account = $wp_admin_bar->get_node('my-account');
    // Check if the 'my-account' node exists
    if ($my_account) {
        $args = array(
            'id' => 'last_login',
            'parent' => 'my-account',
            'title' => _('Last login: ') . get_user_meta(get_current_user_id(), '_last_login_text', true),
        );
        $wp_admin_bar->add_node($args);
    }
}
add_action('admin_bar_menu', 'last_login_admin_bar', 999);

//---------------END LAST LOGIN TIME----------------------------------------------//


//---------------BEGIN PAGINATION FUNCTION ----------------------------------------------//
function iplus_paginate($current_page = 1, $max_pages = 0) {

    $pages_links = paginate_links(array(
        'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
        'format' => 'page/%#%',
        'type' => 'plain',
        'current' => max(1, $current_page),
        'total' => $max_pages
            ));

    return $pages_links;
}
//---------------END PAGINATION FUNCTION ----------------------------------------------//



//Remove default actions that output stuff to header you don't need. Comment the line if you want to allow the action.

//remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
//remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
//remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
//remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
//remove_action('wp_head', 'index_rel_link'); // Index link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // Prev link
remove_action('wp_head', 'start_post_rel_link', 10, 0); // Start link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // Display relational links for the posts adjacent to the current post.
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
//remove_action('wp_head', 'rel_canonical');
//remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', 'wp_generator'); //Remove <meta name="generator" content="WordPress 3.6" /> from header


//---------------  HELPER FUNCTIONS ----------------------------------------------------- //
//Get a custom size thumbnail url with phpthumb
function iplus_get_custom_thumbnail_url($thumb_id, $width = 0, $height = 0) {
    if (($thumbnail = wp_get_attachment_url($thumb_id)) !== false) {
        if (intval($width) > 0 && intval($height) > 0)
            return esc_attr('/thumb.php?zc=1&w=' . $width . '&h=' . $height . '&src=' . parse_url($thumbnail, PHP_URL_PATH));
        elseif (intval($width) > 0)
            return esc_attr('/thumb.php?w=' . $width . '&src=' . parse_url($thumbnail, PHP_URL_PATH));
        elseif (intval($height) > 0)
            return esc_attr('/thumb.php?h=' . $height . '&src=' . parse_url($thumbnail, PHP_URL_PATH));
        else
            return $thumbnail; //return full image url
    }

    return '';
}

//Limit the number of tags shown. Good to maintain your layout in lists. Still, show ALL tags in the single post page.
function the_tags_limit($before = '', $separator = ', ', $after = '', $limit = 4) {
    $posttags = get_the_tags();
    $count = 1;
    $sep = '';
    if ($posttags) {
        echo $before;
        foreach ($posttags as $tag) {
            echo $sep . '<a href="' . get_tag_link($tag->term_id) . '">' . $tag->name . '</a>';
            $sep = $separator;
            $count++;
            if ($count > absint($limit)) {
                if ($count <= count($posttags))
                    echo ' ...';
                break; //change the number to adjust the count
            }
        }
        echo $after;
    }
}

/**
 * Tests if any of a post's assigned categories are descendants of target categories
 *
 * @param int|array $cats The target categories. Integer ID or array of integer IDs
 * @param int|object $_post The post. Omit to test the current post in the Loop or main query
 * @return bool True if at least 1 of the post's categories is a descendant of any of the target categories
 * @see get_term_by() You can get a category by name or slug, then pass ID to this function
 * @uses get_term_children() Passes $cats
 * @uses in_category() Passes $_post (can be empty)
 * @version 2.7
 * @link http://codex.wordpress.org/Function_Reference/in_category#Testing_if_a_post_is_in_a_descendant_category
 */
if (!function_exists('post_in_parent_cat')) {

    function post_in_parent_cat($cats, $_post = null) {

        foreach ((array) $cats as $cat) {
            // get_term_children() accepts integer ID only
            if (in_category((int) $cat, $_post)) {
                return true;
            } else {
                $descendants = get_term_children((int) $cat, 'category');
                if ($descendants && in_category($descendants, $_post))
                    return true;
            }
        }

        return false;
    }

}


function the_breadcrumb() {

  global $wp_query;
  $separator = '&gt;';

  echo '<ul id="breadcrumbs">';

  if($wp_query->is_home()) {
          echo '<li><a href="'.get_home_url().'">Home</a></li><li class="separator"> '.$separator.' </li>';
          echo '<li>';
              echo the_title();
          echo '</li>';
  }
  elseif($wp_query->is_page()) {
          echo '<li><a href="'.get_home_url().'">Home</a></li><li class="separator"> '.$separator.' </li>';
          echo '<li>';
              echo the_title();
          echo '</li>';
  }
  elseif($wp_query->is_category() ){
      echo '<li><a href="'.get_home_url().'">Home</a></li><li class="separator"> '.$separator.' </li>';
      echo '<li>';
      the_category(' </li><li class="separator"> '.$separator.' </li><li> ');
  }
  elseif( $wp_query->is_single()){
      echo '<li><a href="'.get_home_url().'">Home</a></li><li class="separator"> '.$separator.' </li>';
      echo '<li>';
        the_category(' </li><li class="separator"> '.$separator.' </li><li> ');
        echo '</li><li class="separator"> '.$separator.' </li><li>';
        the_title();
      echo '</li>';
  }
  elseif (is_tag()) {single_tag_title();}
  elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';}
  elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
  elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
  elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
  elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
  elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}

  echo '</ul><br>';
}

?>