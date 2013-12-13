<?php
/*
Template Name: Blog
*/
global $wp_query;
$paged = get_query_var('paged');


$args=array(
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 6,
    'post_status' => 'publish',
    'post_type' => 'post',
    'paged' => $paged
);

$q = new WP_Query($args);

//We check if the user is requesting a page that does not exist.
if( $paged > $q->max_num_pages ){
    $wp_query->set_404();
    status_header( 404 );
    load_template(get_404_template());
    exit();
}

get_header();
$wp_query = $q; //save query in global wp_query so that we can use template tags
?>

        <div class="main-container">
            <div class="main wrapper clearfix">

                <div id="content"><p>Total <?=$wp_query->found_posts?> posts</p>
                <?php while ( have_posts() ) : the_post(); ?>

                            <!-- start post -->
                            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                <header>
                                    <?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
                                        <?php the_post_thumbnail('thumbnail',array('style'=>'float:left;margin-right:10px;height:auto;width:64px;', 'class'=>'lazy','src'=>get_template_directory_uri().'/img/transparent.gif','data-original'=>wp_get_attachment_thumb_url( get_post_thumbnail_id() )));  ?>
                                    <?php endif; ?>
                                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <div>in <?php the_category(', ') ?> by <?php the_author_posts_link(); ?> &bull; <?php the_date(); ?> <span class="count-comments"><?php comments_number( 'No comments yet', '1 comment', '% comments' ); ?></span></div>
                                </header>
                                <section>
                                    <?php the_excerpt(); ?>
                                    <p><a href="<?php the_permalink(); ?>" >Read more <span>&rarr;</span></a></p>
                                </section>
                            </article>
                            <!-- end post -->

                <?php endwhile; ?>

                </div>

                <?php get_sidebar() ?>

                 <!-- pagination -->
                <section id="pagination" >

                    <?php if( intval($paged) + 1 <= $wp_query->max_num_pages ): ?>
                    <div style="padding:0 0 10px 0;" id="loadmore" ><a class="loadmore" href="<?=get_pagenum_link( max(array( intval($paged) + 1,2)) )?>">Load more</a></div>
                    <?php endif; ?>

                    <?php
                    echo iplus_paginate($paged, $wp_query->max_num_pages);
                    wp_reset_query(); //Will call wp_reset_post_data
                    ?>

                </section>

            </div> <!-- #main -->



        </div>

<!-- start footer -->
<?php get_footer(); ?>
<!-- end footer -->

</body>
</html>