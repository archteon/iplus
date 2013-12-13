<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains comments and the comment form.
 *
*/

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() )
    return;
?>

<!-- start latest comments -->

<?php if(have_comments()): ?>

    <div>

        <h4>Latest Comments (<?php comments_number( '', '1 comment total', '% comments total' ); ?>)</h4>

        <?php

        //$comments = get_comments('status=approve&post_id='.get_the_ID());
        $comments = get_comments('type=comment&order=ASC&post_id='.get_the_ID());

        // Generate your multidimensional array from the linear array
        function GenerateNestedCommentsArray($arr, $parent = 0){
            $comms = array();
            foreach($arr as $com){
                if(intval($com->comment_parent) == $parent){
                    $com->children = isset($com->children) ? $com->children : GenerateNestedCommentsArray($arr, $com->comment_ID);
                    $comms[] = $com;
                }
            }
            return $comms;
        }

        // loop the multidimensional array recursively to generate the HTML
        function GenerateCommentsHTML($nav, $depth=1){

            global $comment;

            foreach($nav as $comment):
                /* Comment tags use the $GLOBALS['comment'] variable if it is defined */
        ?>

                <!--  comment -->
                <div <?php comment_class(); ?> id="comment-<?=get_comment_ID()?>" >

                        <?=get_avatar($comment,32)?>
                        <div><a href="<?=get_comment_link( $comment->comment_ID )?>"><?php comment_author( ); ?></a> said on <span><?php comment_date(); echo ' at '; comment_time(); ?> </span> : <?php edit_comment_link(__('Edit'),'  ','' ); ?> <?php comment_reply_link(array('depth' => $depth, 'max_depth' => get_option('thread_comments_depth'), 'add_below'=>'comment-text', 'before'=> '' )); ?></div>
                        <div>
                            <?php if ($comment->comment_approved == '0') : ?>
                                <b><?php _e('Your comment is awaiting moderation.') ?></b>
                            <?php else: ?>
                                <?php comment_text( );?>
                                <div><span id="comment-text-<?=get_comment_ID()?>"></span></div>
                            <?php endif; ?>

                                <?php if(count($comment->children)>0): /* Display children comments. Each level adds 10px padding. Style as you wish.*/ ?>
                                <div style="padding-left: 10px">
                                    <?php GenerateCommentsHTML($comment->children,$depth+1); ?>
                                </div>
                                <?php endif; ?>

                        </div>

                </div>

        <?php
            endforeach;
        }

        $navarray = GenerateNestedCommentsArray($comments);
        GenerateCommentsHTML($navarray);

        ?>


    </div>

<?php endif; ?>

<!-- end latest comments -->

<!-- start comment's form -->
<div>
    <?php comment_form(array( 'title_reply' => 'Add a new Comment' )); ?>
</div>
<!-- end comment's form -->
