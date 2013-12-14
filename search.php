<?php
get_header();
global $wp_query;
?>

        <div class="main-container">
            <div class="main wrapper clearfix">

                <?php
                    if(intval(get_query_var('paged'))==0)
                        wp_insert_post(array('post_type'=>'search_queries', 'post_status'=>'private', 'post_excerpt'=>'','post_title'=> $wp_query->found_posts.' results for query: '. addslashes( trim(strip_tags(get_search_query(false))) ) ));
                ?>

                <div id="content"><p>Total <?=$wp_query->found_posts?> results</p>

                <?php if(have_posts()): ?>

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


                <?php else: _e('Nothing matched your search terms. Please, try again.'); endif; ?>

                <h3>Didn't find what you were looking for?</h3>
                <?php get_search_form(); ?>

                </div>

                <div id="rightcol">
                <?php get_sidebar() ?>
                </div>

                <?php if($wp_query->max_num_pages>1): ?>
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
                <?php endif; ?>

            </div> <!-- #main -->



        </div>

<!-- start footer -->
<?php get_footer(); ?>
<!-- end footer -->

</body>
</html>