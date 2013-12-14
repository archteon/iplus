<?php
/* Template Name: Contact Page */

$response = "";

//function to generate response
function generate_response($type, $message){
  global $response;

  if($type == "success")
      $response = "<div class='success'>{$message}</div>";
  else
      $response = "<div class='error'>{$message}</div>";

}

if(isset($_POST['submitted']) && $_POST['submitted']=='923fA'){

    //response messages
    $not_human       = "Human verification failed.";
    $missing_content = "Please, fill in all required fields.";
    $email_invalid   = "E-mail address is invalid.";
    $message_unsent  = "Failed to send message. Please, try again.";
    $message_sent    = "Thank you! Your message has been sent.";

    //user posted variables
    $name = sanitize_text_field($_POST['namee']);
    $email = sanitize_email($_POST['email']);
    $subject = sanitize_text_field($_POST['subject']);
    $message = sanitize_text_field($_POST['message']);
    $human = intval($_POST['message_human']);
    
    
    //php mailer variables
    $to = get_option('admin_email');
    if(trim($subject)=='')
        $subject = "Contact form submitted from ".get_bloginfo('name');
    $headers[] = "From: $name <$email>";
    $headers[] = "Reply-To: $name <$email>";
    
    $posted_message = 'Name: '."$name\r\n";
    $posted_message .= 'Email: '."$email\r\n\r\n";
    $posted_message .= 'Subject: '."$subject\r\n\r\n";
    $posted_message .= $message;

    if($human != 0){

        if($human != 3)
            generate_response("error", $not_human); //not human!
        else {

            //validate email
            if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                generate_response("error", $email_invalid);
            else {
                //validate presence of name and message
                if(empty($name) || empty($message)){
                    generate_response("error", $missing_content);
                }
                else{
                    $sent = wp_mail($to, $subject, wp_strip_all_tags($posted_message), $headers);
                    if($sent)
                        generate_response("success", $message_sent); //message sent!
                    else
                        generate_response("error", $message_unsent); //message wasn't sent
                    
                }
            }
        }

    }
    else
        generate_response("error", $missing_content);

}
get_header();
?>

<div class="main-container">
        <div class="main wrapper clearfix">

                <?php while ( have_posts() ) : the_post(); ?>

                            <!-- start post -->
                            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                                <h2>Contact</h2>

                                <div><?=isset($response)?$response:''?></div>

                                <!-- start form -->
                                <form name="contactme" method="post" action="<?php the_permalink(); ?>" >

                                    <div>
                                        <p><b>Name</b> <span>(required)</span></p>
                                        <input required="required" placeholder="Your full name" type="text" name="namee" value="<?=isset($_POST['namee'])?esc_attr($_POST['namee']):''; ?>" />
                                    </div>
                                    <div>
                                        <p><b>Email</b> <span>(required)</span></p>
                                        <input required="required" type="email" placeholder="Type a valid e-mail address" name="email" value="<?=isset($_POST['email'])?esc_attr($_POST['email']):''; ?>" />
                                    </div>
                                    <div>
                                        <p><b>Subject</b></p>
                                        <input type="text" name="subject" value="<?=isset($_POST['subject'])?esc_attr($_POST['subject']):''; ?>" />
                                    </div>
                                    <div>
                                        <p><b>Your Message</b> <span>(required)</span></p>
                                        <textarea required="required" name="message"><?=isset($_POST['message'])?esc_textarea($_POST['message']):''; ?></textarea>
                                    </div>
                                    <div>
                                        <p><b>Human Verification</b> <span>(required)</span></p>
                                        <input required="required" maxlength="3" type="text" style="width:50px;" name="message_human" value="<?=isset($_POST['message_human'])?esc_attr($_POST['message_human']):''; ?>" /> + 2 = 5
                                    </div>
                                    <div>
                                        <p>
                                            <input type="hidden" name="submitted" value="923fA">
                                            <input id="submit" type="submit" name="submit" value="Send Message" />
                                        </p>
                                    </div>

                                </form>
                                <!-- end form -->

                            </article>
                            <!-- end post -->

                <?php endwhile; ?>

                <div id="rightcol">
                <?php get_sidebar() ?>
                </div>

            </div> <!-- #main -->

</div>

<!-- start footer -->
<?php get_footer(); ?>
<!-- end footer -->

</body>
</html>