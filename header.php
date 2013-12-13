<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php wp_title( '|', true, 'right' ); ?></title>
        <meta name="description" content="<?php bloginfo('description') ?>">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="<?=get_template_directory_uri() ?>/css/normalize.min.css">
        <link rel="stylesheet" href="<?=get_template_directory_uri() ?>/css/main.css">

        <script src="<?=get_template_directory_uri() ?>/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>

        <?php wp_head(); ?>

    </head>
    <body <?php body_class(); ?>>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

        <div class="header-container">
            <header class="wrapper clearfix">
                <h1 class="title"><?php bloginfo('name') ?></h1>
                <!-- start menu -->
                <?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => 'nav') ); ?>
            </header>
        </div>

