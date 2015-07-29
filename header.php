<!DOCTYPE html>
<html>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!--[if lte IE 8]>
        <link rel="stylesheet" href="<?= THEME_TEMPLATE_DIRECTORY; ?>/style-ie8.css">
    <![endif]-->
    <!--[if gt IE 8]><!-->
        <link rel="stylesheet" href="<?= THEME_TEMPLATE_DIRECTORY; ?>/style.css">
    <!--<![endif]-->

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

    <?php wp_nav_menu( array( 'theme_location' => 'main' ) ); ?>