<?php
global $wp_query;
$paged = get_query_var('paged');
get_header();
?>

        <div class="main-container">
            <div class="main wrapper clearfix">

                <div id="content"><p>Total <?=$wp_query->found_posts?> posts</p><?php the_breadcrumb(); ?>
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

                <div id="rightcol">
                <?php get_sidebar() ?>
                </div>

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