<!DOCTYPE html>
<html <?php language_attributes('html'); ?>><head>
    <meta http-equiv="Content-Type" content="<?php echo get_bloginfo('html_type') .'; charset=' . get_bloginfo('charset'); ?>">
    <title><?php echo tarski_doctitle(); ?></title>
    <?php wp_head(); ?>
</head>

<body id="<?php echo tarski_bodyid(); ?>" <?php body_class(); ?>>

<script type="text/javascript">
    (function() {
        jQuery('body').addClass('js');
    })();
</script>

<div id="wrapper" class="tarski">
    <div id="header">
        <?php th_header(); ?>
    </div>
    
    <div id="content" class="clearfix">
