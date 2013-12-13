<?php
/**
 * The sidebar containing the widget area, displays on posts and pages.
 *
 */

if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
    <!-- start sidebar -->
    <?php dynamic_sidebar('sidebar-1'); ?>
    <?php wp_meta(); ?>
    <!-- end sidebar -->
<?php else: ?>

    <!-- start sidebar -->
    <aside>
        <h3>Aside</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales urna non odio egestas tempor. Nunc vel vehicula ante. Etiam bibendum iaculis libero, eget molestie nisl pharetra in. In semper consequat est, eu porta velit mollis nec. Curabitur posuere enim eget turpis feugiat tempor. Etiam ullamcorper lorem dapibus velit suscipit ultrices.</p>
    </aside>
    <!-- end sidebar -->

<?php endif; ?>
