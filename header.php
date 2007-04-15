<?php @include(TEMPLATEPATH . '/constants.php'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php if(defined(WPLANG)) { language_attributes(); } else { echo 'xml:lang="en" lang="en"'; } ?>>

<head><title><?php tarski_title(); ?></title>

	<meta http-equiv="Content-Type" content="<?php echo get_bloginfo('html_type'); ?>; charset=<?php echo get_bloginfo('charset'); ?>" />
	<meta name="generator" content="WordPress <?php echo get_bloginfo('version'); ?>" />
	<meta name="robots" content="all" />
	<meta name="description" content="<?php echo get_bloginfo('description'); ?>" />
	
	<link rel="stylesheet" href="<?php echo get_bloginfo('stylesheet_url'); ?>" type="text/css" media="screen,projection" />
<?php if(get_bloginfo('text_direction') == 'rtl') { ?>
	<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/library/rtl.css" type="text/css" media="screen,projection" />
<?php } ?>
	<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/library/print.css" type="text/css" media="print" />
<?php if(get_tarski_option('style')) { ?>
	<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/styles/<?php echo get_tarski_option('style'); ?>" type="text/css" media="screen,projection" />
<?php } ?>

<?php if (is_single()) { ?>
	<?php if(get_settings('permalink_structure')) { // Feed link hack ?>
	<link rel="alternate" type="application/rss+xml" title="Comments feed" href="<?php the_permalink() ?>feed/" />
	<?php } else { ?>
	<link rel="alternate" type="application/rss+xml" title="Comments feed" href="<?php the_permalink() ?>&amp;feed=rss" />
	<?php } ?>
<?php } ?>
	<link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> feed" href="<?php echo get_bloginfo('rss2_url'); ?>" />

<?php wp_head();
echo $headerInclude; ?>
</head>

<body class="<?php tarski_bodyclass(); ?>"><div id="wrapper">

<div id="header" class="<?php echo tarski_header_status(); ?>">

	<?php tarski_headerimage(); ?>
	
	<?php if(get_tarski_option('display_title') != 'lolno' || get_tarski_option('display_tagline')) { ?>
	<div id="title">
		<?php if(get_tarski_option('display_title') != 'lolno') { tarski_title('header'); }
		if((get_bloginfo('description') != '') && get_tarski_option('display_tagline')) { echo '<p id="tagline">' .  get_bloginfo('description') . '</p>'; } ?>
	</div>
	<?php } ?>

	<div id="navigation">
		<ul class="primary">
			<?php tarski_navbar(); ?>
		</ul>

		<div class="secondary">
			<p><a class="feed" href="<?php echo get_bloginfo_rss('rss2_url'); ?>"><?php _e('Subscribe to feed', 'tarski'); ?></a></p>
		</div>
	</div>

</div>

<div id="content">