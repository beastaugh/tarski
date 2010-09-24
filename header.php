<!DOCTYPE html>
<html <?php language_attributes('html'); ?>><head>
    <meta http-equiv="Content-Type" content="<?php echo get_bloginfo('html_type') .'; charset=' . get_bloginfo('charset'); ?>">
    <title><?php echo tarski_doctitle(); ?></title>
    <?php wp_head(); ?>
</head>

<body id="<?php tarski_bodyid(); ?>" <?php body_class(); ?>>

<div id="wrapper">
    <div id="header">
        <?php th_header(); ?>
    </div>

    <div id="content" class="clearfix">
