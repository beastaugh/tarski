<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php if(defined(WPLANG)) { language_attributes(); } else { echo 'xml:lang="en" lang="en"'; } ?>>

<head><title><?php echo tarski_doctitle(); ?></title>

	<meta http-equiv="Content-Type" content="<?php echo get_bloginfo('html_type') .'; charset=' . get_bloginfo('charset'); ?>" />
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
	<meta name="wp_theme" content="Tarski <?php echo theme_version(); ?>" />
	<?php if(get_bloginfo('description')) { ?><meta name="description" content="<?php bloginfo('description'); ?>" /><?php } ?>
	
	<?php wp_head(); ?>

</head>

<body id="<?php tarski_bodyid(); ?>" class="<?php tarski_bodyclass(); ?>"><div id="wrapper">

<div id="header">

	<?php th_header(); ?>

	<div id="navigation">
		<?php th_navbar(); ?>
	</div>

</div>

<div id="content">