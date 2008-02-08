<?php
/**
 * class Asset
 * 
 * @package Tarski
 * @since 2.1
 */
class Asset {
	
	function init() {
		$assets = new Asset;
		
		$assets->meta();
		$assets->stylesheets();
		$assets->javascript();
		$assets->feeds();
		
		$assets->output();
	}
	
	function meta() {
		// Theme name and version
		$themeversion = theme_version();
		$meta = array("<meta name=\"wp_theme\" content=\"Tarski $themeversion\" />");
		
		// Description
		global $wp_query;
		$excerpt = trim(strip_tags(wp_specialchars($wp_query->post->post_excerpt)));

		if ( (is_single() || is_page()) && strlen($excerpt) )
			$description = $excerpt;
		else
			$description = get_bloginfo('description');

		if ( strlen($description) )
			$meta[] = "<meta name=\"description\" content=\"$description\" />";
		
		// Robots
		if(get_option('blog_public') != '0')
			$meta[] = '<meta name="robots" content="all" />';
			
		$this->meta = apply_filters('tarski_asset_meta', $meta);
	}
	
	function stylesheets() {
		// Default stylesheets
		$style_array = array(
			'main' => array(
				'url' => get_bloginfo('stylesheet_url'),
			),
			'print' => array(
				'url' => get_bloginfo('template_directory') . '/library/css/print.css',
				'media' => 'print'
			),
			'mobile' => array(
				'url' => get_bloginfo('template_directory') . '/library/css/mobile.css',
				'media' => 'handheld'
			)
		);

		// Adds the alternate style, if one is selected
		if(get_tarski_option('style')) {
			$style_array['alternate'] = array(
				'url' => get_bloginfo('template_directory') . '/styles/' . get_tarski_option('style')
			);
		}

		// The more complex array can be filtered if desired
		$style_array = apply_filters('tarski_style_array', $style_array);

		// The business end of the function
		if(is_array($style_array)) {
			foreach($style_array as $type => $values) {
				// URL is required
				if(is_array($values) && $values['url']) {
					if(!($media = $values['media'])) {
						$media = 'screen,projection';
					}
					$stylesheets[$type] = sprintf(
						'<link rel="stylesheet" href="%1$s" type="text/css" media="%2$s" />',
						$values['url'],
						$media
					);
				}
			}
		}

		$this->stylesheets = apply_filters('tarski_stylesheets', $stylesheets);
	}
	
	function javascript() {
		$scripts = array(
			'tarski-js' => get_bloginfo('template_directory') . '/library/js/tarski-js.php'
		);

		foreach($scripts as $name => $url) {
			$javascript[$name] = "<script type=\"text/javascript\" src=\"$url\"></script>";
		}

		$this->javascript = apply_filters('tarski_javascript', $javascript);
	}
	
	function feeds() {
		if(is_single() || (is_page() && ($comments || comments_open()))) {
			global $post;
			$title = sprintf(__('Commments feed for %s','tarski'), get_the_title());
			$link = get_post_comments_feed_link($post->ID, $type);
			$source = 'post_comments';
		} elseif(is_archive()) {
			if(is_category()) {
				$title = sprintf( __('Category feed for %s','tarski'), single_cat_title('','',false) );
				$link = get_category_feed_link(get_query_var('cat'), $type);
				$source = 'category';
			} elseif(is_tag()) {
				$title = sprintf( __('Tag feed for %s','tarski'), single_tag_title('','',false));
				$link = get_tag_feed_link(get_query_var('tag_id'), $type);
				$source = 'tag';
			} elseif(is_author()) {
				$title = sprintf( __('Articles feed for %s','tarski'), the_archive_author_displayname());
				$link = get_author_feed_link(get_query_var('author'), $type);
				$source = 'author';
			} elseif(is_date()) {
				if(is_day()) {
					$title = sprintf( __('Daily archive feed for %s','tarski'), tarski_date());
					$link = get_day_link(get_the_time('Y'), get_the_time('m'), get_the_time('d'));
					$source = 'day';
				} elseif(is_month()) {
					$title = sprintf( __('Monthly archive feed for %s','tarski'), get_the_time('F Y'));
					$link = get_month_link(get_the_time('Y'), get_the_time('m'));
					$source = 'month';
				} elseif(is_year()) {
					$title = sprintf( __('Yearly archive feed for %s','tarski'), get_the_time('Y'));
					$link = get_year_link(get_the_time('Y'));
					$source = 'year';
				}	
				if(get_settings('permalink_structure')) {
					if( function_exists('get_default_feed') || ($type == 'rss2') ) {
						$link .= 'feed/';
					} else {
						$link .= "feed/$type/";
					}
				} else {
					$link .= '&amp;feed=' . get_default_feed();
				}
			}
		} elseif(is_search()) {
			$search_query = attribute_escape(get_search_query());
			$feeds['search'] = generate_feed_link( sprintf(__('Search feed for %s','tarski'), $search_query), get_search_feed_link('', $type), feed_link_type($type) );
			$title = sprintf(__('Search comments feed for %s','tarski'), $search_query);
			$link = get_search_comments_feed_link('', $type);
			$source = 'search_comments';
		}

		if($title && $link)
			$feeds[$source] = generate_feed_link($title, $link, feed_link_type($type));

		$feeds['site'] = generate_feed_link( sprintf(__('%s feed','tarski'), get_bloginfo('name')), get_feed_link(), feed_link_type($type) );
		
		$this->feeds = apply_filters('tarski_feeds', $feeds);
	}
	
	function output() {
		$assets_collapsed = array();
		
		// Implode each array with carriage returns, two between asset groups
		foreach ( $this as $asset ) {
			$assets_collapsed[] = implode("\n", $asset);
		}
		
		$assets_filtered = apply_filters('tarski_assets', $assets_collapsed);
		echo implode("\n\n", $assets_filtered);
	}
}

?>