<?php get_header() ?>

        <div class="main-container">
            <div class="main wrapper clearfix">

                <div id="content">

                    <?php the_breadcrumb(); ?>

                    <?php if (have_posts()): ?>

                        <?php while(have_posts()): the_post(); ?>

                                <!-- start post -->
                                <article id="post-<?php the_ID() ?>" <?post_class()?>>
                                    <header>
                                        <h3><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h3>
                                        <div>in <?php the_category(', ') ?> by <?php the_author_posts_link() ?> &bull; <?php the_date() ?></div>
                                    </header>
                                    <section>
                                        <?php if( has_post_thumbnail() && !post_password_required() ) : ?>
                                            <?php the_post_thumbnail('medium') ?>
                                        <?php endif; ?>
                                        <?php the_content() ?>
                                    </section>
                                </article>
                                <!-- end post -->

                        <?php endwhile; ?>

                        <?php comments_template(); ?>

                    <?php endif; ?>

                </div>

                <div id="rightcol">
                <?php get_sidebar() ?>
                </div>

            </div> <!-- #main -->
        </div> <!-- #main-container -->

        <!-- start footer -->
        <?php get_footer() ?>
        <!-- end footer -->

    </body>
</html>
