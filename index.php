<?php
/**
 * The main template file.
 *
*/
get_header();
?>

        <div class="main-container">
            <div class="main wrapper clearfix">

                <div id="content">

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

                    <!-- pagination -->
                    <article>
                        <?php
                            echo iplus_paginate($paged, $wp_query->max_num_pages);
                            wp_reset_query(); //Will call wp_reset_post_data
                        ?>
                    </article>

                </div>

                <?php get_sidebar() ?>

            </div>



        </div> <!-- #main -->

        <!-- start footer -->
        <?php get_footer(); ?>
        <!-- end footer -->

    </body>
</html>
