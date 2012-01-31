<!DOCTYPE html>
<html <?php language_attributes('html'); ?>><head>
    <meta http-equiv="Content-Type" content="<?php echo get_bloginfo('html_type') .'; charset=' . get_bloginfo('charset'); ?>">
    <title><?php echo wp_title('&middot;'); ?></title>
    <?php wp_head(); ?>
</head>

<body id="<?php echo tarski_bodyid(); ?>" <?php body_class(); ?>>

<div id="wrapper" class="tarski">
    <div id="header">
        <?php th_header(); ?>
    </div>
    
    <div id="content" class="clearfix">
