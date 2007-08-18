<?php

/**
  * FeedParser - v0.4
  * Copyright (C) 2004-2007 Niels Leenheer
  *
  * This program is free software; you can redistribute it and/or
  * modify it under the terms of version 2 of the GNU General 
  * Public License as published by the Free Software Foundation
  *
  */




/* Define status codes */
define ("STATUS_OK", 1);
define ("STATUS_NOT_MODIFIED", 2);
define ("STATUS_ERROR_FETCH", 3);
define ("STATUS_ERROR_ENCODING", 4);
define ("STATUS_ERROR_PARSE", 5);
define ("STATUS_ERROR_FORMAT", 6);



/*********************************************************************
 * Helper class for all parsers, formats and extensions
 */

class ParserBase {

	function _debug ($string) {
		echo $string."<br />";
	}
	
	function _error ($string) {
		echo $string."<br />";
	}

	function attribute($attributes, $tagname, $namespace = null) {
		while (list($k,$v) = each($attributes))
			if ($v['name'] == $tagname && ($namespace == null || $v['namespace'] == $namespace))
				return $v['value'];
	}

	function _splitOnNamespace ($tag) {
		if (preg_match("%^(.+):([A-Z0-9\.]+)$%i", $tag, $regs)) 
			return array ($regs[1], $regs[2]);

		return array ('', $tag);
	}

	function _calculateUrl ($path, $url) {

		if ($path == '') return $url;
		if ($url == '') return $path;

		$path = parse_url($path);
		$url  = trim($url);

		if (!isset($path['path']) || $path['path'] == '') 
			$path['path'] = '/';
		elseif (substr($path['path'], strlen($path['path']) - 1, 1) != '/') 
			$path['path'] = substr($path['path'], 0, strrpos($path['path'], '/')) . '/';
			
		if (substr($url, 0, 2) == './')
			$url = substr($url, 2);
	
		if ($url == '') {
			$result = '';
		}
		elseif (preg_match('/^[a-z]+\:/i', $url)) {
			$result = $url;
		} 
		elseif (substr($url, 0, 2) == '//') {
			$result = $path['scheme'] . ':' . $url;
		}
		elseif (substr($url, 0, 1) == '/') {
			$result = $path['scheme'] . '://' . $path['host'] . $url;
		}
		elseif ($url == '.') {
			$result = $path['scheme'] . '://' . $path['host'] . $path['path'];
		}
		else {
			$result = $path['scheme'] . '://' . $path['host'] . $path['path'] . $url;
		}

		while (preg_match ("|([^/])/[^/]+/\.\./|", $result)) 
			$result = preg_replace ("|([^/])/[^/]+/\.\./|", "\\1/", $result);

		return $result;
	}
}


class XMLParserTree extends ParserBase {

	var $namespaces;
	var $tags;
	var $tree;
	
	var $_currentElement;
	var $_currentDepth;
	var $_parentElements;

	var $unknownCounter = 0;
	
	function XMLParserTree($base) {
		$this->tree = array(
			'language' => '',
			'base' => $base
		);

		$this->tags = array();
		$this->namespaces = array(
			'http://www.w3.org/XML/1998/namespace' => 'xml'
		);

		$this->_currentElement = & $this->tree;
		$this->_currentDepth = 0;
		$this->_parentElements[$this->_currentDepth] = & $this->tree;
	}

	function startElement($parser, $tag, $attributes) 
	{
		list($namespace, $name) = $this->_splitOnNamespace($tag);
		$this->registerNamespace($namespace);
		$prefix = $namespace != '' ? $this->namespaces[$namespace] : '';

		$language = isset($attributes['http://www.w3.org/XML/1998/namespace:lang']) ? 
			$attributes['http://www.w3.org/XML/1998/namespace:lang'] : $this->_currentElement['language'];

		$base = isset($attributes['http://www.w3.org/XML/1998/namespace:base']) ? 
			$this->_calculateUrl($this->_currentElement['base'], $attributes['http://www.w3.org/XML/1998/namespace:base']) : $this->_currentElement['base'];
		
		$current = array (
			'type' => 'open',
			'name' => $name,
			'prefix' => $prefix,
			'namespace' => $namespace,
			'depth' => $this->_currentDepth,
			'language' => $language,
			'base' => $base
		);
		
		while (list($attribute, $value) = each($attributes)) 
		{
			list($namespace, $name) = $this->_splitOnNamespace($attribute);
			$this->registerNamespace($namespace);
			$prefix = $namespace != '' ? $this->namespaces[$namespace] : '';
			
			$current['attributes'][] = array(
				'name' => $name,
				'prefix' => $prefix,
				'namespace' => $namespace,
				'value' => $value
			); 
		}
		
		// Create a new level...
		$this->_currentElement['children'][] = & $current;
		$this->_currentElement = & $current;

		$this->_currentDepth++;
		$this->_parentElements[$this->_currentDepth] = & $this->_currentElement;
		$this->tags[$this->_currentElement['name']][] = & $this->_currentElement;
	}
	
	function endElement($parser, $tag) 
	{
		// Drop back one level...
		if (isset($this->_currentElement['children']) &&
			count($this->_currentElement['children']) == 1 &&
			$this->_currentElement['children'][0]['type'] == 'cdata') 
		{
			$this->_currentElement['value'] = $this->_currentElement['children'][0]['value'];
			unset($this->_currentElement['children']);
		}
		
		if (!isset($this->_currentElement['children']))
			$this->_currentElement['type']  = 'complete';

		$this->_currentDepth--;
		$this->_currentElement = & $this->_parentElements[$this->_currentDepth];
	}

	function cdata($parser, $cdata) 
	{
		if (isset($this->_currentElement['children']) && count($this->_currentElement['children']))
			$previous = & $this->_currentElement['children'][count($this->_currentElement['children']) - 1];

		if (isset($previous) && $previous['type'] == 'cdata')
			$previous['value'] .= $cdata;
		else
			$this->_currentElement['children'][] = array (
				'type' => 'cdata',
				'depth' => $this->_currentDepth,
				'namespace' => $this->_currentDepth > 0 ? $this->_parentElements[$this->_currentDepth]['namespace'] : '',
				'value' => $cdata
			);
	}

	function namespace($parser, $prefix, $uri) {
		$this->namespaces[$uri] = $prefix;
	}
	
	function registerNamespace($uri) {
		if (!isset($this->namespaces[$uri])) {
			$this->unknownCounter++;
			$prefix = 'unknown' . $this->unknownCounter;
			$this->namespaces[$uri] = $prefix;
		}
	}
}


/*********************************************************************
 * XML Parser class
 */

class XMLParserBase extends ParserBase {
	
	function Parse ($raw, $base = '') {

		$tree = new XMLParserTree($base);

		$parser = xml_parser_create_ns('UTF-8');
		xml_set_object($parser, $tree);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
		xml_set_element_handler($parser, "startElement", "endElement");
		xml_set_character_data_handler($parser, "cdata");
		xml_set_start_namespace_decl_handler($parser, "namespace");

		if (!xml_parse($parser, $raw)) {
	    	die(sprintf("XML error: %s at line %d\n",
    			xml_error_string(xml_get_error_code($parser)),
            	xml_get_current_line_number($parser)));
		}

		xml_parser_free($parser);	
		
		// Output...
		if (isset($this->tree['children']))
			$this->tree = & $tree->tree['children'];
		else
			$this->tree = array();

		$this->namespaces = & $tree->namespaces;
		$this->tags = & $tree->tags;
	}
}


/*********************************************************************
 * Feed Parser class (UTF-8 string based)
 */

class FeedParserBase extends ParserBase {

	var $customNamespaces = array();

	function Parse ($raw, $base = '') 
	{
		// First call the XML Parser to do the actual parsing
		$parser = new XMLParserBase;
		$parser->Parse($raw, $base);

		$tree = & $parser->tree;
		$namespaces = & $parser->namespaces;
		$tags = & $parser->tags;

		$meta = array();
		$feed = array();

		// Secondly we need to detect the type of feed we are dealing with,
		// because each type of feed needs a different interpreter.
		
		// RSS 0.90 & RSS 1.0
		if (isset($namespaces['http://www.w3.org/1999/02/22-rdf-syntax-ns#']) && isset($tags['RDF']))
		{
			if (isset($namespaces['http://purl.org/rss/1.0/']) && isset($tags['channel'])) {
				$meta['type'] = 'RSS';
				$meta['version'] = 1.0;
				
				$f = new FeedParserRdfChannel();
				$f->customNamespaces = $this->customNamespaces;
				$f->parseElementChannel($feed, $tags['channel'][0]);
				
				if (isset($tags['item'])) {
					while (list($k,) = each ($tags['item'])) {
						$entry = array();

						$e = new FeedParserRdfItem();
						$e->customNamespaces = $this->customNamespaces;
						$e->parseElementItem($entry, $tags['item'][$k]);

						$this->cleanupEntry($feed, $entry);
						$feed['entries'][] = $entry;
					}
				}

				$this->cleanupFeed($feed);
			}
			
			elseif (isset($namespaces['http://my.netscape.com/rdf/simple/0.9/']) && isset($tags['channel'])) {
				$meta['type'] = 'RSS';
				$meta['version'] = 0.9;

				$f = new FeedParserRdfChannel();
				$f->customNamespaces = $this->customNamespaces;
				$f->parseElementChannel($feed, $tags['channel'][0]);
				
				if (isset($tags['item'])) {
					while (list($k,) = each ($tags['item'])) {
						$entry = array();

						$e = new FeedParserRdfItem();
						$e->customNamespaces = $this->customNamespaces;
						$e->parseElementItem($entry, $tags['item'][$k]);

						$this->cleanupEntry($feed, $entry);
						$feed['entries'][] = $entry;
					}
				}
	
				$this->cleanupFeed($feed);
			}
		}
	
		// RSS 1.1
		elseif (isset($namespaces['http://purl.org/net/rss1.1#']) && isset($tags['Channel'])) {
			$meta['type'] = 'RSS';
			$meta['version'] = 1.1;

			$f = new FeedParserRdfChannel();
			$f->customNamespaces = $this->customNamespaces;
			$f->parseElementChannel($feed, $tags['Channel'][0]);
			
			if (isset($tags['item'])) {
				while (list($k,) = each ($tags['item'])) {
					$entry = array();

					$e = new FeedParserRdfItem();
					$e->customNamespaces = $this->customNamespaces;
					$e->parseElementItem($entry, $tags['item'][$k]);

					$this->cleanupEntry($feed, $entry);
					$feed['entries'][] = $entry;
				}
			}

			$this->cleanupFeed($feed);
		}

		// RSS 0.91, 0.92, 0.93, 0.94 & 2.0
		elseif (isset($tags['rss'])) {
			$meta['type'] = 'RSS';

			if ($version = $this->attribute($tags['rss'][0]['attributes'], 'version'))
				$meta['version'] = floatval($version);
			else
				$meta['version'] = 'Unknown';

			$f = new FeedParserRssChannel();
			$f->customNamespaces = $this->customNamespaces;
			$f->parseElementChannel($feed, $tags['channel'][0]);
			
			if (isset($tags['item'])) {
				while (list($k,) = each ($tags['item'])) {
					$entry = array();

					$e = new FeedParserRssItem();
					$e->customNamespaces = $this->customNamespaces;
					$e->parseElementItem($entry, $tags['item'][$k]);

					$this->cleanupEntry($feed, $entry);
					$feed['entries'][] = $entry;
				}
			}

			$this->cleanupFeed($feed);
		}
		
		// Atom 1.0 Full feed
		elseif (isset($namespaces['http://www.w3.org/2005/Atom']) && isset($tags['feed'])) {
			$meta['type'] = 'Atom';
			$meta['version'] = 1.0;

			$f = new FeedParserAtomFeed();
			$f->customNamespaces = $this->customNamespaces;
			$f->parseElementFeed($feed, $tags['feed'][0]);
			
			if (isset($tags['entry'])) {
				while (list($k,) = each ($tags['entry'])) {
					$entry = array();

					$e = new FeedParserAtomEntry();
					$e->customNamespaces = $this->customNamespaces;
					$e->parseElementEntry($entry, $tags['entry'][$k]);

					$this->cleanupEntry($feed, $entry);
					$feed['entries'][] = $entry;
				}
			}

			$this->cleanupFeed($feed);
		}

		// Atom 1.0 Single entry
		elseif (isset($namespaces['http://www.w3.org/2005/Atom']) && isset($tags['entry'])) {
			$meta['type'] = 'Atom (Single Entry)';
			$meta['version'] = 1.0;

			$entry = array();

			$e = new FeedParserAtomEntry();
			$e->customNamespaces = $this->customNamespaces;
			$e->parseElementEntry($entry, $tags['entry'][0]);

			$feed['entries'][] = $entry;
		}

		// Atom 0.2, 0.3 and various other drafts
		elseif (isset($tags['feed'])) 
		{
			$meta['type'] = 'Atom';

			if (preg_match("/^(http:\/\/purl.org\/atom\/ns#(.+)?)$/i", $tags['feed'][0]['namespace'], $regs))
			{
				if (isset($regs[2]) && preg_match("/^draft\-ietf\-atompub\-format\-([0-9]+)$/i", $regs[2], $matches))
					$meta['version'] = 'Draft ' . (int)$matches[1];
				
				elseif ($version = $this->attribute($tags['feed'][0]['attributes'], 'version'))
					$meta['version'] = floatval($version);
				
				else 
					$meta['version'] = 'Unknown';
			}

			$f = new FeedParserAtomFeed();
			$f->customNamespaces = $this->customNamespaces;
			$f->parseElementFeed($feed, $tags['feed'][0]);
			
			if (isset($tags['entry'])) {
				while (list($k,) = each ($tags['entry'])) {
					$entry = array();

					$e = new FeedParserAtomEntry();
					$e->customNamespaces = $this->customNamespaces;
					$e->parseElementEntry($entry, $tags['entry'][$k]);

					$this->cleanupEntry($feed, $entry);
					$feed['entries'][] = $entry;
				}
			}
			
			$this->cleanupFeed($feed);
		}
	
		return (array (
			'meta' => $meta,
			'feed' => $feed
		));
	}
	
	function cleanupGeneric(& $entry) 
	{
		/* Remove duplicates from category */
		if (isset($entry['category']))
			$entry['category'] == array_unique($entry['category']);


		if (isset($entry['comments'])) 
		{
			if (isset($entry['comments']['href'])) {
				$entry['links'][] = array (
					'rel' => 'replies',
					'href' => $entry['comments']['href'],
					'type' => 'text/html'
				);
			}

			if (isset($entry['comments']['feed'])) {
				$entry['links'][] = array (
					'rel' => 'replies',
					'href' => $entry['comments']['feed'],
					'type' => 'application/rss+xml'
				);
			}
		}

		/* Prepare links */
		if (isset($entry['links']))
		{
			$result = array();
			$items  = array();	
			$rels   = array('alternate', 'self', 'related', 'via', 'enclosure', 'replies', 'license');

			// First group all links
			while (list(,$link) = each ($entry['links'])) 
			{
				$hash = md5(
					(isset($link['rel']) ?  $link['rel'] : '') . '-' .
					(isset($link['href']) ?  $link['href'] : '') . '-' .
					(isset($link['type']) ?  $link['type'] : '') . '-' .
					(isset($link['hreflang']) ?  $link['hreflang'] : '') . '-' .
					(isset($link['title']) ?  $link['title'] : '') . '-' .
					(isset($link['length']) ?  $link['length'] : '')
				);

				$items[$hash] = $link;			
				$rels[] = $link['rel'];
			}
			
			$rels = array_unique($rels);

			while (list(,$rel) = each($rels)) 
			{
				reset ($items);
				while (list($hash,$item) = each ($items))
				{
					if ($item['rel'] == $rel) 
					{
						$result[] = $item;
					}
				}
			}

			if (count($result))
			{
				$entry['links'] = $result;
				$permalink = false;
				
				reset ($entry['links']);
				while (list(,$link) = each ($entry['links']))
					if (isset($link['permalink']) && $link['permalink']) $permalink = true;


				if (!$permalink)
				{
					if (count($entry['links']) == 1) 
					{
						$entry['links'][0]['permalink'] = true;
						$permalink = true;
					}
				}	
					
				if (!$permalink) 
				{
					reset ($entry['links']);
					while (list($key,$link) = each ($entry['links']))
					{
						if (!$permalink && $link['rel'] == 'alternate' && isset($link['type']) && ($link['type'] == 'text/html' || $link['type'] == 'application/xhtml+xml')) 
						{
							$entry['links'][$key]['permalink'] = true;
							$permalink = true;
						}
					}
				}

				if (!$permalink) 
				{
					reset ($entry['links']);
					while (list($key,$link) = each ($entry['links']))
					{
						if (!$permalink && $link['rel'] == 'alternate') 
						{
							$entry['links'][$key]['permalink'] = true;
							$permalink = true;
						}
					}
				}
				
				if (!$permalink) 
				{
					reset ($entry['links']);
					while (list($key,$link) = each ($entry['links']))
					{
						if (!$permalink && isset($entry['id']) && $link['href'] == $entry['id']) 
						{
							$entry['links'][$key]['permalink'] = true;
							$permalink = true;
						}
					}
				}

				if (!$permalink) 
				{
					reset ($entry['links']);
					while (list($key,$link) = each ($entry['links']))
					{
						if (!$permalink && $link['rel'] == 'self' && isset($link['type']) && ($link['type'] == 'text/html' || $link['type'] == 'application/xhtml+xml')) 
						{
							$entry['links'][$key]['permalink'] = true;
							$permalink = true;
						}
					}
				}

				if (!$permalink) 
				{
					reset ($entry['links']);
					while (list($key,$link) = each ($entry['links']))
					{
						if (!$permalink && $link['rel'] == 'self') 
						{
							$entry['links'][$key]['permalink'] = true;
							$permalink = true;
						}
					}
				}

				if ($permalink) 
				{
					reset ($entry['links']);
					while (list($key,$link) = each ($entry['links']))
					{
						if (isset($link['permalink']) && $link['permalink'] == 'true') 
						{
							$entry['link'] = & $entry['links'][$key];
						}
					}
				}
			}
		}			
	}

	function cleanupFeed(& $feed)
	{
		// Do generic stuff...
		$this->cleanupGeneric($feed);
	}
		
	function cleanupEntry(& $feed, & $entry) 
	{
		/* Populate author */
		if (!isset($entry['author']) && isset($entry['source']['author'])) 
			$entry['author'] = & $entry['source']['author'];

		if (!isset($entry['author']) && isset($feed['author'])) 
			$entry['author'] = & $feed['author'];

		// Do generic stuff...
		$this->cleanupGeneric($entry);
	}
	
	function addCustomNamespace ($namespace, $prefix) {
		$this->customNamespaces[$namespace] = $prefix;
	}
}


/*********************************************************************
 * Feed Parser class (string based, converts foreign charsets)
 */

class FeedParserCharset extends FeedParserBase {

	function Parse ($raw, $encoding = '', $base = '') {

		if ($encoding == '')
		{
			$str = substr($raw, 0, 4096);
			$len = strlen($str);
			$pos = 0;
			$out = '';
			
			while ($pos < $len)
			{
				$ord = ord($str[$pos]);
				
				if ($ord > 32 && $ord < 128)
					$out .= $str[$pos];
					
				$pos++;
			}
	
			// Check meta inside document
			if (preg_match ("/;\s*charset=([^\"']+)/is", $out, $regs))
				$encoding = strtolower(trim($regs[1]));

			// Then check xml declaration
			if (preg_match("/<\?xml.+encoding\s*=\s*[\"|']([^\"']+)[\"|']\s*\?>/i", $out, $regs))
				$encoding = strtolower(trim($regs[1]));
		}

		// If encoding is still empty...
		if ($encoding == '')
			$encoding = 'utf-8';

		// Convert to UTF-8
		$raw = utf8::convert($raw, $encoding);
		
		// Process result
		$result = FeedParserBase::Parse($raw, $base);
		$result['meta']['encoding'] = $encoding;
		
		return $result;
	}
}


/*********************************************************************
 * Feed Parser class (URL based)
 */

class FeedParserURL extends FeedParserCharset {

	var $etag_out;
	var $contents;
	var $response;
	var $charset;
	var $baseurl;
	
	var $useragent;
	var $accept;
		
	function RSSAtomParserURL () {
		$this->useragent = "LibFeedParser/0.4 (for " . $_SERVER['HTTP_HOST'] . ")";
		$this->accept    = "application/atom+xml,application/rdf+xml,application/rss+xml,application/xml;q=0.9,text/xml;q=0.2,*/*;q=0.1";
	}
	
	function Parse ($url, $etag = '') {
		
		if (substr($url, 0, 5) == 'feed:')
			$url = substr($url, 5);

		$this->GetURL($url, $etag);
		
		if ($this->response == '200')
		{
			$result = FeedParserCharset::Parse($this->contents, $this->charset, $url);
			$result['meta']['feed'] = $url;
			$result['meta']['response'] = $this->response;
			$result['meta']['etag'] = $this->etag_out;
		}
		else
		{
			$result = array (
				'meta' => array(),
				'channel' => array(),
				'items' => array()
			);
		
			$result['meta']['feed'] = $url;
			$result['meta']['response'] = $this->response;

			if ($this->response == '304')
			{
				$result['meta']['status'] = STATUS_NOT_MODIFIED;
			}
			else
			{
				$result['meta']['status'] = STATUS_ERROR_FETCH;
				$result['meta']['verbose'] = 'Could not retrieve feed';
			}
		}
		
		return $result;
	}

	function GetURL ($url, $etag = '') {

		if ((function_exists('curl_init')) && (ini_get('safe_mode') == 'Off'))
		{
			$header = array (
				"Accept: " . $this->accept
			);
		
			if ($etag != '')
				$header[] = 'If-None-Match: "'.$etag.'"';
		
			// Set options
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
			
			// Retrieve response
			$this->contents = curl_exec($ch);
			$info 		    = curl_getinfo($ch);
			$this->response = $info['http_code'];
			
			// Split into headers and contents
			$this->headers = substr($this->contents, 0, $info['header_size']);
			$this->contents = substr($this->contents, $info['header_size']);
			
			// Check if ETtag is present
			if (preg_match ('/ETag:[^"]+"([^\n]+)"/is', $this->headers, $regs))
				$this->etag_out = trim($regs[1]);
			
			if (preg_match ("/charset=([^\n]+)/is", $this->headers, $regs))
				$this->charset = strtolower(trim($regs[1]));
			
			curl_close($ch);
		}
		else
		{
			if ($this->contents = @file_get_contents($url))
				$this->response = '200';
			else
				$this->response = '404';
			
			$this->etag_out = '';
		}
	}
	
	function setUseragent($ua) {
		$this->useragent = $ua;
	}
}


/*********************************************************************
 * Helper class for all formats and extensions
 */

class FeedParserHelper extends ParserBase {

	var $customNamespaces = array();
	
	function iterateChildren(& $context, & $tag, $namespace) {

		if (count($tag)) {
			reset ($tag);
			while (list($k,) = each ($tag)) {
				if ($tag[$k]['namespace'] == $namespace) {
					if (isset($tag[$k]['name'])) {
						$methodName = 'parseElement' . ucfirst($this->_tagnameToName($tag[$k]['name']));
	
						if (method_exists($this, $methodName)) {
							call_user_func_array(array($this, $methodName), array(& $context, & $tag[$k]));
						} elseif (method_exists($this, 'parse')) {
							call_user_func_array(array($this, 'parse'), array(& $context, & $tag[$k]));
						}
					}
				} else {
					if ($name = $this->_namespaceToName($tag[$k]['namespace'])) {
						$className  = 'FeedParserExtension' . ucfirst($name);
						$methodName = 'parseElement' . ucfirst($this->_tagnameToName($tag[$k]['name']));
	
						if (class_exists($className)) {
							$extension = new $className;
							if (method_exists($extension, $methodName)) {
								call_user_func_array(array($extension, $methodName), array(& $context, & $tag[$k]));
							} elseif (method_exists($extension, 'parseElement')) {
								call_user_func_array(array($extension, 'parseElement'), array(& $context, & $tag[$k]));
							}
						}
					}
				}
			}
		}
	}
	
	function iterateAttributes(& $context, & $tag) {
	
		if (isset($tag['attributes']) && count($tag['attributes'])) {
			reset ($tag['attributes']);
			while (list($k,) = each ($tag['attributes'])) {
				if ($tag['attributes'][$k]['namespace'] != '' && $tag['attributes'][$k]['namespace'] != $tag['namespace']) {
					if ($name = $this->_namespaceToName($tag['attributes'][$k]['namespace'])) {
						$className  = 'FeedParserExtension' . ucfirst($name);
						$methodName = 'parseAttribute' . ucfirst($this->_tagnameToName($tag['attributes'][$k]['name']));					
						
						if (class_exists($className)) {
							$extension = new $className;
							if (method_exists($extension, $methodName)) {
								call_user_func_array(array($extension, $methodName), array(& $context, & $tag, & $tag['attributes'][$k]));
							} elseif (method_exists($extension, 'parseAttribute')) {
								call_user_func_array(array($extension, 'parseAttribute'), array(& $context, & $tag, & $tag['attributes'][$k]));
							}
						}
					}
				}
			}
		}
	}
	
	function _tagnameToName($tagname) {
		$components = explode('.', $tagname);

		if (count($components) > 1) {
			while (list($k,) = each ($components)) {
				$components[$k] = ucfirst($components[$k]);
			}
			return implode('', $components);
		}
		
		return $tagname;
	}
	
	function _namespaceToName($namespace) {
	
		$namespaces = array (
			"http://www.w3.org/1999/xhtml"							=> 'xhtml',
			"http://www.w3.org/1998/Math/MathML"					=> 'mathml',
			"http://www.w3.org/2000/svg"							=> 'svg',

			"http://purl.org/syndication/thread/1.0"				=> 'thr',
			"http://purl.org/dc/elements/1.1/"						=> 'dc',
			"http://purl.org/dc/terms/"								=> 'dcterms',
			"http://purl.org/rss/1.0/modules/aggregation/" 			=> 'ag',
			"http://purl.org/rss/1.0/modules/annotate/" 			=> 'annotate',
			"http://purl.org/rss/1.0/modules/content/" 				=> 'content',
			"http://purl.org/rss/1.0/modules/company/" 				=> 'company',
			"http://purl.org/rss/1.0/modules/email/" 				=> 'email',
			"http://purl.org/rss/1.0/modules/event/" 				=> 'ev',
			"http://purl.org/rss/1.0/modules/image/" 				=> 'image',
			"http://purl.org/rss/1.0/modules/link/" 				=> 'l',
			"http://purl.org/rss/1.0/modules/reference/" 			=> 'ref',
			"http://purl.org/rss/1.0/modules/richequiv/" 			=> 'reqv',
			"http://purl.org/rss/1.0/modules/rss091#" 				=> 'rss091',
			"http://purl.org/rss/1.0/modules/search/" 				=> 'search',
			"http://purl.org/rss/1.0/modules/slash/" 				=> 'slash',
			"http://purl.org/rss/1.0/modules/servicestatus/" 		=> 'ss',
			"http://purl.org/rss/1.0/modules/subscription/" 		=> 'sub',
			"http://purl.org/rss/1.0/modules/syndication/" 			=> 'sy',
			"http://purl.org/rss/1.0/modules/taxonomy/" 			=> 'taxo',
			"http://purl.org/rss/1.0/modules/threading/" 			=> 'thr',
			"http://purl.org/rss/1.0/modules/wiki/" 				=> 'wiki',
			"http://purl.org/net/rss1.1/payload#" 					=> 'p',
			"http://backend.userland.com/blogChannelModule" 		=> 'blogChannel',
			"http://backend.userland.com/creativeCommonsRssModule" 	=> 'creativeCommons',
			"http://wellformedweb.org/CommentAPI/" 					=> 'wfw',
			"http://madskills.com/public/xml/rss/module/trackback/" => 'trackback',
			"http://xmlns.com/foaf/0.1/" 							=> 'foaf',
			"http://web.resource.org/cc/" 							=> 'cc',
			"http://my.theinfo.org/changed/1.0/rss/" 				=> 'cp',
			"http://www.georss.org/georss"							=> 'geo',
			"http://www.w3.org/2003/01/geo/wgs84_pos#" 				=> 'geow3',
			"http://geourl.org/rss/module/" 						=> 'geourl',
			"http://postneo.com/icbm" 								=> 'icbm',
			"http://hacks.benhammersley.com/rss/streaming/" 		=> 'str',
			"http://schemas.xmlsoap.org/soap/envelope/" 			=> 'soap',
			"http://webns.net/mvcb/" 								=> 'admin',
			"http://prismstandard.org/namespaces/1.2/basic/" 		=> 'prism',
			"http://prismstandard.org/namespaces/pcv/1.2" 			=> 'pcv',
			"http://media.tangent.org/rss/1.0/" 					=> 'audio',
			"http://search.yahoo.com/mrss/"							=> 'media',
			"http://www.itunes.com/DTDs/Podcast-1.0.dtd" 			=> 'itunes',
			"http://rssnamespace.org/feedburner/ext/1.0"			=> 'feedburner',
		);
	
		$namespaces = array_merge($namespaces, $this->customNamespaces);
	
		if (isset($namespaces[$namespace]))
			return $namespaces[$namespace];
	}

	function _parseMimeType($string) {
		return $string;
	}

	function _parseInteger($string) {
		return intval($string);
	}
	
	function _parseLength($string) {
		return $string;
	}

	function _parseLanguage($string) {
		return $string;
	}

	function _parseAuthor ($string) {

		$result = array();

		if (preg_match("/^(.+)\s\((.+)\)$/", $string, $matches))
		{
			if (substr($matches[1], 0, 7) == 'mailto:') 
				$result['email'] = substr($matches[1], 7);
			elseif ($this->_is_email($matches[1]))
				$result['email'] = $matches[1];
			else
				$result['name'] = $matches[1];

			if (substr($matches[2], 0, 7) == 'mailto:') 
				$result['email'] = substr($matches[2], 7);
			elseif ($this->_is_email($matches[2]))
				$result['email'] = $matches[2];
			else
				$result['name'] = $matches[2];
		}
		else
		{
			if ($this->_is_email($string))
				$result['email'] = $string;
			else
				$result['name'] = $string;
		}

		return $result;
	}
	
	function _parseUrl ($url, $base = '') {
		return $this->_calculateUrl ($base, $url);
	}
	
	function _parseEmail ($email) {
		$email = trim($email);

		if ($this->_is_email($email))
			return $email;
	}
	
	function _parseDate ($date_str) {

		$pat = "/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(?::(\d{2})(?:\.(\d+))?)?(?:([-+])(\d{2}):?(\d{2})|(Z))?/";
		
		if (preg_match( $pat, $date_str, $match)) 
		{
			list( $year, $month, $day, $hours, $minutes, $seconds) = 
				array( $match[1], $match[2], $match[3], $match[4], $match[5], $match[6]);
			
			// Calc epoch for current date assuming GMT
			$epoch = gmmktime( $hours, $minutes, $seconds, $month, $day, $year);
			
			$offset = 0;
			
			if (!isset($match[11]) || $match[11] != 'Z') 
			{
				list( $tz_mod, $tz_hour, $tz_min ) =
					array( $match[8], $match[9], $match[10]);
				
				// Zero out the variables
				if ( ! $tz_hour ) { $tz_hour = 0; }
				if ( ! $tz_min ) { $tz_min = 0; }
			
				$offset_secs = (($tz_hour*60)+$tz_min)*60;
				
				// Is timezone ahead of GMT?  then subtract offset
				if ( $tz_mod == '+' ) {
					$offset_secs = $offset_secs * -1;
				}
				
				$offset = $offset_secs;	
			}

			$epoch = $epoch + $offset;
			
			return $epoch;
		}
		else 
		{
			$result = strtotime($date_str);
			if ($result != -1) return $result;
		}
	}
	
	function _parseStringAtom ($tag)
	{
		$string = trim($tag['value']);

		if (isset($tag['attributes'])) {
			$mode = $this->attribute($tag['attributes'], 'mode') | '';
			$type = $this->attribute($tag['attributes'], 'type') | '';
		} else {
			$mode = '';
			$type = '';
		}

		if ($mode != '') 
		{
			$mode = strtolower ($mode);
			
			if ($type == 'text/plain')
			{
				if ($mode == 'base64') {
					$string = base64_decode($string);
				}

				$mode = 'xml';
			}

			if (substr($type, 0, 5) == 'text/')
			{
				if ($mode == 'base64') {
					$string = base64_decode($string);
				}

				$mode = 'escaped';
			}

			if (substr($type, -4) == '+xml' || substr($type, -4) == '/xml')
			{
				if ($mode == 'base64') {
					$string = base64_decode($string);
				}

				$mode = 'xml';
			}

			if ($type == 'text/plain' && $mode == 'xml') $type = 'text';
			if ($type == 'text/html' && $mode == 'escaped') $type = 'html';
			if ($type == 'application/xhtml+xml' && $mode == 'xml') $type = 'xhtml';
		}
		
		if ($type == '')
			$type = 'text';
		
		return $this->_parseString ($string, $type);
	}
	
	function _parseString ($string, $type = '')
	{
		$type = strtolower($type);
		
		// Autodetect type if needed
		if ($type == '')
			$type = $this->_detectType($string);

		if ($type == 'text') {
			$string = trim($string);
		} 
		
		elseif ($type == 'html' || $type == 'xhtml') {
			$string = trim($string);

			if ($type == 'html') {

				// Tidy
				if (class_exists('tidy')) 
				{
					$tidy = new tidy;
					$tidy->parseString($string, array (
						'bare'			=> true,
						'clean'			=> true,
						'drop-proprietary-attributes' => true,
						'output-xhtml'  => true,
						'indent'        => true,
						'wrap'          => 200
					), 'utf8');
					$tidy->cleanRepair();
					$string = $tidy->value;
				}

				if (preg_match('/<body>(.*)<\/body>/iUs', $string, $matches)) {
				   $string = $matches[1];
				}
			}
		}
		
		elseif (substr($type, 0, 5) == 'text/' || substr($type, -4) == '+xml' || substr($type, -4) == '/xml') {
			$string = trim($string);
		}

		else {
			$string = base64_decode(trim($string));
		}
		
		
		if ($string != '')
		{
			return array(
				'type' => $type,
				'value' => $string
			);
		}
	}

	function _detectType($data) {
		$html = strip_tags($data) != $data;
		$html = $html || html_entity_decode ($data) != $data;
		$html = $html || preg_match("/&#[Xx]?([0-9A-Fa-f]+);/", $data);
		return $html ? 'html': 'text';
	}

    function _is_email($email_address) {     
		return preg_match("/[\w\-][^@]+\@[\w\-][^@]+\.[\w\-][^@]+/", $email_address) && true;
    }    
}


/*********************************************************************
 * RSS 0.9, RSS 1.0 and RSS 1.1 (RDF based)
 */

class FeedParserRdfChannel extends FeedParserHelper {
	
	function parseElementChannel(& $context, &$tag) {
		$this->iterateChildren($context, $tag['children'], $tag['namespace']);

		if (!isset($context['language']) && $tag['language'] != '')
			$context['language'] = $this->_parseLanguage($tag['language']);
	}

	function parseElementTitle(& $context, & $tag) {
		if (!isset($context['title']) && isset($tag['value']) && $title = $this->_parseString($tag['value']))
			$context['title'] = $title;
	}
	
	function parseElementDescription(& $context, & $tag) {
		if (!isset($context['subtitle']) && isset($tag['value']) && $subtitle = $this->_parseString($tag['value']))
			$context['subtitle'] = $subtitle;
	}

	function parseElementLink(& $context, & $tag) {
		$link = array ();
		$link['href'] = $this->_parseUrl($tag['value'], $tag['base']);
		$link['rel'] = 'alternate';
		$context['links'][] = $link;
	}
}

class FeedParserRdfItem extends FeedParserHelper {
	
	function parseElementItem(& $context, &$tag) {
		$this->iterateChildren($context, $tag['children'], $tag['namespace']);

		if (isset($tag['attributes']) && $about = $this->attribute($tag['attributes'], 'about', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#')) 
			$context['id'] = $about;
		
		if (!isset($context['language']) && $tag['language'] != '')
			$context['language'] = $this->_parseLanguage($tag['language']);
	}

	function parseElementTitle(& $context, & $tag) {
		if (!isset($context['title']) && isset($tag['value']) && $title = $this->_parseString($tag['value']))
			$context['title'] = $title;
	}
	
	function parseElementDescription(& $context, & $tag) {
		if (!isset($context['summary']) && isset($tag['value']) && $summary = $this->_parseString($tag['value']))
			$context['summary'] = $summary;
	}

	function parseElementLink(& $context, & $tag) {
		if (isset($tag['value'])) {
			$link = array ();
			$link['href'] = $this->_parseUrl($tag['value'], $tag['base']);
			$link['rel'] = 'alternate';
			$context['links'][] = $link;
		}
	}
}


/*********************************************************************
 * RSS 0.91+ and RSS 2.0 (non-RDF based)
 */

class FeedParserRssChannel extends FeedParserHelper {

	function parseElementChannel(& $context, &$tag) {
		$this->iterateChildren($context, $tag['children'], $tag['namespace']);

		if (!isset($context['language']) && $tag['language'] != '')
			$context['language'] = $this->_parseLanguage($tag['language']);
	}

	function parseElementTitle(& $context, & $tag) {
		if (!isset($context['title']) && isset($tag['value']) && $title = $this->_parseString($tag['value']))
			$context['title'] = $title;
	}
	
	function parseElementDescription(& $context, & $tag) {
		if (!isset($context['subtitle']) && isset($tag['value']) && $subtitle = $this->_parseString($tag['value']))
			$context['subtitle'] = $subtitle;
	}

	function parseElementLink(& $context, & $tag) {
		if (isset($tag['value'])) {
			$link = array ();
			$link['href'] = $this->_parseUrl($tag['value'], $tag['base']);
			$link['rel'] = 'alternate';
			$context['links'][] = $link;
		}
	}
	
	function parseElementLanguage(& $context, & $tag) {
		if (isset($tag['value']))
			$context['language'] = $this->_parseLanguage($tag['value']);
	}

	function parseElementCopyright(& $context, & $tag) {
		if (!isset($context['rights']) && isset($tag['value']) && $rights = $this->_parseString($tag['value']))
			$context['rights'] = $rights;
	}
	
	function parseElementManagingEditor(& $context, & $tag) {
		if (isset($tag['value']) && $editor = $this->_parseAuthor($tag['value'])) {
			if (isset($context['author']))
				$context['contributor'][] = $editor;
			else
				$context['author'] = $editor;
		}
	}

	function parseElementWebMaster(& $context, & $tag) {
		if (isset($tag['value']) && $webmaster = $this->_parseAuthor($tag['value'])) {
			if (isset($context['author']))
				$context['contributor'][] = $webmaster;
			else
				$context['author'] = $webmaster;
		}
	}
	
	function parseElementPubDate(& $context, & $tag) {
		if (isset($tag['value']) && $published = $this->_parseDate($tag['value']))
			$context['published'] = $published;
	}

	function parseElementLastBuildDate(& $context, & $tag) {
		if (isset($tag['value']) && $updated = $this->_parseDate($tag['value']))
			$context['updated'] = $updated;
	}

	function parseElementCategory(& $context, & $tag) {
		if (isset($tag['value'])) {
			$category = array();
			$category['term'] = $tag['value'];

			if (isset($tag['attributes']) && $scheme = $this->attribute($tag['attributes'], 'domain')) 
				$category['scheme'] = $scheme;

			$context['category'][] = $category;					
		}
	}

	function parseElementGenerator(& $context, & $tag) {
		if (!isset($context['generator']['uri']) && isset($tag['value']))
			$context['generator']['uri'] = $this->_parseUrl($tag['value']);
	}

	function parseElementImage(& $context, & $tag) {
		if (!isset($context['logo'])) {
			$logo = array();
			$this->iterateChildren($logo, $tag['children'], $tag['namespace']);

			if (isset($logo['uri']))
				$context['logo'] = $logo;
		}
	}

	function parseElementUrl(& $context, & $tag) {
		if (!isset($context['uri']) && isset($tag['value']) && $uri = $this->_parseUrl($tag['value'], $tag['base'])) {
			$context['uri'] = $uri;
		}
	}
}

class FeedParserRssItem extends FeedParserHelper {

	function parseElementItem(& $context, &$tag) {
		$this->iterateChildren($context, $tag['children'], $tag['namespace']);

		if (!isset($context['language']) && $tag['language'] != '')
			$context['language'] = $this->_parseLanguage($tag['language']);
	}

	function parseElementGuid(& $context, & $tag) {
		if (isset($tag['value'])) {
			$context['id'] = $tag['value'];
	
			if (isset($tag['attributes'])) {
				if (! $isPermaLink = $this->attribute($tag['attributes'], 'isPermaLink') || $isPermaLink == 'true')	 
				{
					$link = array ();
					$link['href'] = $tag['value'];
					$link['rel'] = 'alternate';
					$link['permalink'] = true;
					$context['links'][] = $link;
				}	
			}
		}
	}

	function parseElementTitle(& $context, & $tag) {
		if (!isset($context['title']) && isset($tag['value']) && $title = $this->_parseString($tag['value']))
			$context['title'] = $title;
	}
	
	function parseElementDescription(& $context, & $tag) {
		if (!isset($context['summary']) && isset($tag['value']) && $summary = $this->_parseString($tag['value']))
			$context['summary'] = $summary;
	}

	function parseElementLink(& $context, & $tag) {
		if (isset($tag['value'])) {
			$link = array ();
			$link['href'] = $this->_parseUrl($tag['value'], $tag['base']);
			$link['rel'] = 'alternate';
			$context['links'][] = $link;
		}
	}
	
	function parseElementComments(& $context, & $tag) {
		if (isset($tag['value']))
			$context['comments']['href'] = $this->_parseUrl($tag['value'], $tag['base']);
	}
	
	function parseElementPubDate(& $context, & $tag) {
		if (isset($tag['value']) && $published = $this->_parseDate($tag['value']))
			$context['published'] = $published;
	}

	function parseElementAuthor(& $context, & $tag) {
		if (isset($tag['value']) && $author = $this->_parseAuthor($tag['value'])) {
			if (isset($context['author']))
				$context['contributor'][] = $author;
			else
				$context['author'] = $author;
		}
	}

	function parseElementCategory(& $context, & $tag) {
		if (isset($tag['value'])) {
			$category = array();
			$category['term'] = $tag['value'];

			if (isset($tag['attributes']) && $scheme = $this->attribute($tag['attributes'], 'domain')) 
				$category['scheme'] = $scheme;

			$context['category'][] = $category;					
		}
	}
	
	function parseElementEnclosure(& $context, & $tag) {
		$link = array ();
		$link['rel'] = 'enclosure';

		if (isset($tag['attributes']) && $href = $this->attribute($tag['attributes'], 'url'))      
			$link['href'] = $this->_parseUrl($href);

		if (isset($tag['attributes']) && $type = $this->attribute($tag['attributes'], 'type'))     
			$link['type'] = $this->_parseMimeType($type);

		if (isset($tag['attributes']) && $length = $this->attribute($tag['attributes'], 'length')) 
			$link['length'] = $this->_parseLength($length);

		$context['links'][] = $link;
	}

	function parseElementSource(& $context, & $tag) {
		$link = array ();
		$link['rel'] = 'via';

		if (isset($tag['value']))
			$link['title'] = trim($tag['value']);
			
		if (isset($tag['attributes']) && $href = $this->attribute($tag['attributes'], 'url'))      
			$link['href'] = $this->_parseUrl($href);

		$context['links'][] = $link;
	}
}


/*********************************************************************
 * Atom 0.3 and 1.0
 */

class FeedParserAtomCommon extends FeedParserHelper {

	function parseElementId(& $context, & $tag) {
		if (!isset($context['id']) && isset($tag['value'])) 
			$context['id'] = trim($tag['value']);
	}

	function parseElementTitle(& $context, & $tag) {
		if (!isset($context['title']))
		{
			if ($tag['type'] == 'complete' && isset($tag['value']) && $title = $this->_parseStringAtom($tag)) {
				$context['title'] = $title;
			}

			if ($tag['type'] == 'open') {
				$title = array();
				$this->iterateChildren($title, $tag['children'], $tag['namespace']);
				$context['title'] = array_pop($title);
			}
		}
	}

	function parseElementRights(& $context, & $tag) {
		if (!isset($context['rights']))
		{
			if ($tag['type'] == 'complete' && isset($tag['value']) && $rights = $this->_parseStringAtom($tag)) {
				$context['rights'] = $rights;
			}

			if ($tag['type'] == 'open') {
				$rights = array();
				$this->iterateChildren($rights, $tag['children'], $tag['namespace']);
				$context['rights'] = array_pop($rights);
			}
		}
	}
	
	function parseElementUpdated(& $context, & $tag) {
		if (!isset($context['updated']) && isset($tag['value']) && $updated = $this->_parseDate($tag['value']))
			$context['updated'] = $updated;
	}
	
	function parseElementCategory(& $context, & $tag) {
		$category = array ();

		if (isset($tag['attributes']) && $term = $this->attribute($tag['attributes'], 'term')) 
			$category['term'] = trim($term);

		if (isset($tag['attributes']) && $scheme = $this->attribute($tag['attributes'], 'scheme')) 
			$category['scheme'] = $this->_parseUrl($scheme, $tag['base']);

		if (isset($tag['attributes']) && $label = $this->attribute($tag['attributes'], 'label')) 
			$category['label'] = trim($label);

		if (isset($category['term'])) {
			$this->iterateAttributes($category, $tag);
			$context['category'][] = $category;
		}
	}

	function parseElementLink(& $context, & $tag) {
		$link = array ();

		if (isset($tag['attributes']) && $href = $this->attribute($tag['attributes'], 'href')) 
			$link['href'] = $this->_parseUrl($href, $tag['base']);

		if (isset($tag['attributes']) && $type = $this->attribute($tag['attributes'], 'type')) 
			$link['type'] = strtolower(trim($type));

		if (isset($tag['attributes']) && $hreflang = $this->attribute($tag['attributes'], 'hreflang')) 
			$link['hreflang'] = $this->_parseLanguage($hreflang);

		if (isset($tag['attributes']) && $title = $this->attribute($tag['attributes'], 'title')) 
			$link['title'] = trim($title);

		if (isset($tag['attributes']) && $length = $this->attribute($tag['attributes'], 'length')) 
			$link['length'] = $this->_parseLength($length);

		if (isset($link['href'])) 
		{
			if (isset($tag['attributes']) && $rel = $this->attribute($tag['attributes'], 'rel')) 
				$link['rel'] = $rel;
			else 
				$link['rel'] = 'alternate';

			$this->iterateAttributes($link, $tag);
			$context['links'][] = $link;
		}	
	}
	
	function parseElementAuthor(& $context, & $tag) {
		if (!isset($context['author'])) {
			$author = array();
			$this->iterateChildren($author, $tag['children'], $tag['namespace']);

			if (isset($author['name']))
				$context['author'] = $author;
		}
	}

	function parseElementContributor(& $context, & $tag) {
		$contributor = array();
		$this->iterateChildren($contributor, $tag['children'], $tag['namespace']);
		
		if (isset($contributor['name']))
			$context['contributor'][] = $contributor;
	}

	function parseElementName(& $context, & $tag) {
		if (!isset($context['name']) && isset($tag['value'])) {
			$context['name'] = trim($tag['value']);
		}
	}

	function parseElementUri(& $context, & $tag) {
		if (!isset($context['uri']) && isset($tag['value']) && $uri = $this->_parseUrl($tag['value'], $tag['base'])) {
			$context['uri'] = $uri;
		}
	}

	function parseElementEmail(& $context, & $tag) {
		if (!isset($context['email']) && isset($tag['value']) && $email = $this->_parseEmail($tag['value'])) {
			$context['email'] = $email;
		}
	}


	/* Renamed Atom 0.3 elements */
	
	function parseElementModified(& $context, & $tag) {
		$this->parseElementUpdated($context, $tag);
	}

	function parseElementUrl(& $context, & $tag) {
		$this->parseElementUri($context, $tag);
	}
}

class FeedParserAtomFeed extends FeedParserAtomCommon {

	function parseElementFeed(& $context, &$tag) {
		$this->iterateChildren($context, $tag['children'], $tag['namespace']);

		if (!isset($context['language']) && $tag['language'] != '')
			$context['language'] = $this->_parseLanguage($tag['language']);
	}

	function parseElementSubtitle(& $context, & $tag) {
		if (!isset($context['subtitle']))
		{
			if ($tag['type'] == 'complete' && isset($tag['value']) && $subtitle = $this->_parseStringAtom($tag)) {
				$context['subtitle'] = $subtitle;
			}

			if ($tag['type'] == 'open') {
				$subtitle = array();
				$this->iterateChildren($subtitle, $tag['children'], $tag['namespace']);
				$context['subtitle'] = array_pop($subtitle);
			}
		}
	}
	
	function parseElementGenerator(& $context, & $tag) {
		if (!isset($context['generator']) && isset($tag['value'])) {
			$generator = array();
			$generator['value'] = trim($tag['value']);
	
			if (isset($tag['attributes']) && $uri = $this->attribute($tag['attributes'], 'url')) 
				$generator['uri'] = $this->_parseUrl($uri, $tag['base']);

			if (isset($tag['attributes']) && $uri = $this->attribute($tag['attributes'], 'uri')) 
				$generator['uri'] = $this->_parseUrl($uri, $tag['base']);

			if (isset($tag['attributes']) && $version = $this->attribute($tag['attributes'], 'version')) 
				$generator['version'] = trim($version);

			$context['generator'] = $generator;
		}
	}

	function parseElementIcon(& $context, & $tag) {
		if (!isset($context['icon']['uri']) && isset($tag['value']) && $icon = $this->_parseUrl($tag['value'], $tag['base'])) {
			$context['icon']['uri'] = $icon;
		}	
	}
	
	
	/* Renamed Atom 0.3 elements */
	
	function parseElementTagline(& $context, & $tag) {
		$this->parseElementSubtitle($context, $tag);
	}
}

class FeedParserAtomEntry extends FeedParserAtomCommon {

	function parseElementEntry(& $context, &$tag) {
		$this->iterateChildren($context, $tag['children'], $tag['namespace']);

		if (!isset($context['language']) && $tag['language'] != '')
			$context['language'] = $this->_parseLanguage($tag['language']);
	}

	function parseElementSource(& $context, & $tag) {
		if (!isset($context['source']))
		{
			$source = array();
			
			$s = new FeedParserAtomFeed;
			$s->customNamespaces = $this->customNamespaces;
			$s->parseElementFeed($source, $tag);
			
			if (count($source))
				$context['source'] = $source;
		}
	}

	function parseElementSummary(& $context, & $tag) {
		if (!isset($context['summary']))
		{
			if ($tag['type'] == 'complete' && isset($tag['value']) && $summary = $this->_parseStringAtom($tag)) {
				$context['summary'] = $summary;
			}

			if ($tag['type'] == 'open') {
				$summary = array();
				$this->iterateChildren($summary, $tag['children'], $tag['namespace']);
				$context['summary'] = array_pop($summary);
			}
		}
	}

	function parseElementContent(& $context, & $tag) {
		if (!isset($context['content']))
		{
			if (isset($tag['attributes']) && $src = $this->attribute($tag['attributes'], 'src')) 
			{
				$context['content']['src'] = $this->_parseUrl($src, $tag['base']);
				
				if (isset($tag['attributes']) && $type = $this->attribute($tag['attributes'], 'type')) 
					$context['content']['type'] = strtolower(trim($type));
			} 
			else 
			{
				if ($tag['type'] == 'complete' && isset($tag['value']) && $content = $this->_parseStringAtom($tag)) {
					$context['content'] = $content;
				}
	
				if ($tag['type'] == 'open') {
					$content = array();
					$this->iterateChildren($content, $tag['children'], $tag['namespace']);
					$context['content'] = array_pop($content);
				}
			}
		}
	}

	function parseElementPublished(& $context, & $tag) {
		if (!isset($context['published']) && isset($tag['value']) && $published = $this->_parseDate($tag['value']))
			$context['published'] = $published;
	}



	/* Renamed Atom 0.3 elements */
	
	function parseElementIssued(& $context, & $tag) {
		$this->parseElementPublished($context, $tag);
	}
}



/*********************************************************************
 * Inline XHTML
 */

class FeedParserExtensionXml extends FeedParserHelper {

	var $namespace = '';

	function parseElement(& $context, & $tag) {
		if ($content = $this->_parseString($this->printTag($tag), $this->type)) {
			if (isset($context['content']))	{
				$context['content']['value'] .= $content['value'];
			}
			else {
				$context['content'] = $content;
			}
		}
	}
	
	function printChildren (& $tags) {
		$buffer = '';
	
		while (list($k,) = each($tags)) {
			$buffer .= $this->printTag($tags[$k]);
		}	
		
		return $buffer;
	}
	
	function printTag (& $tag) {

		// Add xmlns declaration
		$xmlns = '';

		if ($tag['namespace'] != $this->namespace) {
			$xmlns = ' xmlns="' . $tag['namespace'] .'"';
			$this->namespace = $tag['namespace'];
		}

		$attributes = $xmlns;

		// Add attributes
		$xmlns = '';
		
		if (isset($tag['attributes'])) {
			$namespaces = array();
		
			while (list(,$attribute) = each($tag['attributes'])) 
			{
				if ($value = $this->relativeUrl($tag, $attribute)) {
					$attribute['value'] = $value;
				}
			
				if ($attribute['namespace'] != $this->namespace && $attribute['namespace'] != '') 
				{
					$attributes .= ' ' . $attribute['prefix'] . ':' . $attribute['name'] . '="' . htmlspecialchars($attribute['value']) . '"';
					
					if (!isset($namespaces[$attribute['namespace']])) 
					{
						$namespaces[$attribute['namespace']] = $attribute['prefix'];
						$xmlns .= ' xmlns:' . $attribute['prefix'] . '="' . $attribute['namespace'] . '"';
					}
				} 
				else 
				{
					$attributes .= ' ' . $attribute['name'] . '="' . htmlspecialchars($attribute['value']) . '"';
				}
			}
		}
		
		$attributes .= $xmlns;

		switch ($tag['type']) 
		{
			case 'complete': 
				if (isset($tag['value']))
					return "<".$tag['name'] . $attributes . ">" . htmlspecialchars($tag['value']) . "</" . $tag['name'] . ">";
				else
					return "<".$tag['name'] . $attributes . " />";
				break;

			case 'open': 
				if (isset($tag['value']))
					return "<".$tag['name'] . $attributes . ">" . htmlspecialchars($tag['value']) . $this->printChildren($tag['children']) . "</" . $tag['name'] . ">";
				else
					return "<".$tag['name'] . $attributes . ">" . $this->printChildren($tag['children']) . "</" . $tag['name'] . ">";
				break;

			case 'cdata': 
				return htmlspecialchars($tag['value']);
				break;
		}
		
		return '';
	}

	function relativeUrl (& $tag, & $attribute) {
		if ($attribute['namespace'] == 'http://www.w3.org/1999/xlink' && $attribute['name'] == 'href') {
			if ($href = $this->_parseUrl($attribute['value'], $tag['base'])) {
				return $href;
			}
		}
	}
}



/*********************************************************************
 * Inline XHTML
 */

class FeedParserExtensionXhtml extends FeedParserExtensionXml {

	function parseElement(& $context, & $tag) {
		$this->namespace = $tag['namespace'];
	
		if ($tag['name'] == 'div' || $tag['name'] == 'body') {
			if (isset($tag['children'])) {
				if (!isset($context['content']) && $content = $this->_parseString($this->printChildren($tag['children']), 'xhtml'))
					$context['content'] = $content;
			} 
			
			if (isset($tag['value'])) {
				if (!isset($context['content']) && $content = $this->_parseString(htmlspecialchars($tag['value']), 'xhtml')) {
					$context['content'] = $content;
				}
			}
		} else {
			$this->type = 'xhtml';
			FeedParserExtensionXml::parse($context, $tag);
		}
	}

	function relativeUrl (& $tag, & $attribute) {
	
		if ($attribute['namespace'] == 'http://www.w3.org/1999/xlink' && $attribute['name'] == 'href') {
			if ($href = $this->_parseUrl($attribute['value'], $tag['base'])) {
				return $href;
			}
		}
		
		if (($tag['namespace'] == 'http://www.w3.org/1999/xhtml' && $attribute['namespace'] == '') || $attribute['namespace'] == 'http://www.w3.org/1999/xhtml') {
			switch (true) {
				case ($tag['name'] == 'a' && $attribute['name'] == 'href'):
				case ($tag['name'] == 'img' && $attribute['name'] == 'src'):
				case ($tag['name'] == 'img' && $attribute['name'] == 'longdesc'):
				case ($tag['name'] == 'img' && $attribute['name'] == 'usemap'):
				case ($tag['name'] == 'ins' && $attribute['name'] == 'cite'):
				case ($tag['name'] == 'del' && $attribute['name'] == 'cite'):
				case ($tag['name'] == 'q' && $attribute['name'] == 'cite'):
				case ($tag['name'] == 'blockquote' && $attribute['name'] == 'cite'):
				case ($tag['name'] == 'area' && $attribute['name'] == 'href'):
				case ($tag['name'] == 'iframe' && $attribute['name'] == 'src'):
				case ($tag['name'] == 'object' && $attribute['name'] == 'classid'):
				case ($tag['name'] == 'object' && $attribute['name'] == 'codebase'):
				case ($tag['name'] == 'object' && $attribute['name'] == 'data'):
				case ($tag['name'] == 'object' && $attribute['name'] == 'usemap'):
				case ($tag['name'] == 'applet' && $attribute['name'] == 'codebase'):
				case ($tag['name'] == 'form' && $attribute['name'] == 'action'):
				case ($tag['name'] == 'input' && $attribute['name'] == 'src'):
				case ($tag['name'] == 'input' && $attribute['name'] == 'usemap'):
					if ($href = $this->_parseUrl($attribute['value'], $tag['base'])) {
						return $href;
					}
			}
		}
	}
}


/*********************************************************************
 * Inline SVG
 */

class FeedParserExtensionSvg extends FeedParserExtensionXml {
	var $type = 'image/svg+xml';
}


/*********************************************************************
 * Inline MathML
 */

class FeedParserExtensionMathml extends FeedParserExtensionXml {
	var $type = 'application/mathml+xml';
}


/*********************************************************************
 * Dublin Core
 */

class FeedParserExtensionDc extends FeedParserHelper {

	function parseElementTitle(& $context, & $tag) {
		if (isset($tag['value']) && $title = $this->_parseString($tag['value'])) {
			$context['dc']['title'] = trim($tag['value']);

			if (!isset($context['title']))
				$context['title'] = $title;
		}
	}	

	function parseElementCreator(& $context, & $tag) {
		if (isset($tag['value']) && $author = $this->_parseAuthor($tag['value'])) {
			$context['dc']['creator'] = trim($tag['value']);

			if (!isset($context['author']))
				$context['author'] = $author;
		}
	}
	
	function parseElementSubject(& $context, & $tag) {
		if (isset($tag['value'])) {
			$context['dc']['subject'][] = trim($tag['value']);
			$context['category'][] = array('term' => $tag['value']);
		}
	}

	function parseElementDescription(& $context, & $tag) {
		if (isset($tag['value']) && $description = $this->_parseString($tag['value'])) {
			$context['dc']['title'] = trim($tag['value']);
			/* To do: map to subtitle or tagline... */
		}
	}	

	function parseElementPublisher(& $context, & $tag) {
		if (isset($tag['value']) && $publisher = $this->_parseAuthor($tag['value'])) {
			$context['dc']['publisher'] = trim($tag['value']);
			$context['contributor'] = $publisher;
		}
	}
	
	function parseElementDate(& $context, & $tag) {
		if (isset($tag['value']) && $published = $this->_parseDate($tag['value'])) {
			$context['dc']['date'] = $published;

			if (!isset($context['published']))
				$context['published'] = $published;
		}
	}
	
	function parseElementDateTaken(& $context, & $tag) {
		if (isset($tag['value']) && $published = $this->_parseDate($tag['value'])) {
			$context['dc']['date.taken'] = $published;

			if (!isset($context['published']))
				$context['published'] = $published;
		}
	}
	
	function parseElementType(& $context, & $tag) {
		if (isset($tag['value'])) {
			$context['dc']['type'] = trim($tag['value']);
		}
	}
	
	function parseElementFormat(& $context, & $tag) {
		if (isset($tag['value'])) {
			$context['dc']['format'] = trim($tag['value']);
		}
	}

	function parseElementIdentifier(& $context, & $tag) {
		if (isset($tag['value'])) {
			$context['dc']['identifier'] = trim($tag['value']);
		}
	}
	
	function parseElementSource(& $context, & $tag) {
		if (isset($tag['value'])) {
			$context['dc']['source'] = trim($tag['value']);
		}
	}

	function parseElementLanguage(& $context, & $tag) {
		if (isset($tag['value']) && $language = $this->_parseLanguage($tag['value'])) {
			$context['dc']['language'] = $language;

			if (!isset($context['language']))
				$context['language'] = $language;
		}
	}

	function parseElementRelation(& $context, & $tag) {
		if (isset($tag['value'])) {
			$context['dc']['relation'] = trim($tag['value']);
		}
	}

	function parseElementCoverage(& $context, & $tag) {
		if (isset($tag['value'])) {
			$context['dc']['coverage'] = trim($tag['value']);
		}
	}

	function parseElementRights(& $context, & $tag) {
		if (isset($tag['value']) && $rights = $this->_parseString($tag['value'])) {
			$context['dc']['rights'] = trim($tag['value']);

			if (!isset($context['rights']))
				$context['rights'] = $rights;
		}
	}	
}


/*********************************************************************
 * RSS 1.1 Payload
 */

class FeedParserExtensionP extends FeedParserHelper {
	
	function parseElementPayload(& $context, & $tag) {
		if (!isset($context['content'])) {
			$this->iterateChildren($context, $tag['children'], $tag['namespace']);
		}
	}
}


/*********************************************************************
 * RSS 1.0 Content
 */

class FeedParserExtensionContent extends FeedParserHelper {
	
	function parseElementEncoded(& $context, & $tag) {
		if (!isset($context['content']) && isset($tag['value']) && $content = $this->_parseString($tag['value'], 'html'))
			$context['content'] = $content;
	}
}


/*********************************************************************
 * GeoRSS
 */

class FeedParserExtensionGeo extends FeedParserHelper {
	
	function parseElementPoint(& $context, & $tag) {
		if (isset($tag['value'])) {
			if (preg_match('/([\+\-]?[0-9]+(\.[0-9]+)?)\s+([\+\-]?[0-9]+(\.[0-9]+)?)/', $tag['value'], $matches)) {
				$context['geo']['point']['latitude'] = $matches[1];
				$context['geo']['point']['longitude'] = $matches[3];
			}
		}
	}
	
	function parseElementLine(& $context, & $tag) {
	}
	
	function parseElementPolygon(& $context, & $tag) {
	}
	
	function parseElementBox(& $context, & $tag) {
	}
}


/*********************************************************************
 * Slash
 */

class FeedParserExtensionSlash extends FeedParserHelper {
	
	function parseElementComments(& $context, & $tag) {
		if (!isset($context['comments']['count']) && isset($tag['value']) && $count = $this->_parseInteger($tag['value']))
			$context['comments']['count'] = $count;
	}
}


/*********************************************************************
 * Comment API
 */

class FeedParserExtensionWfw extends FeedParserHelper {
	
	function parseElementComment(& $context, & $tag) {
		if (!isset($context['comments']['api']) && isset($tag['value']) && $api = $this->_parseUrl($tag['value'], $tag['base']))
			$context['comments']['api'] = $api;
	}

	function parseElementCommentRSS(& $context, & $tag) {
		if (!isset($context['comments']['feed']) && isset($tag['value']) && $feed = $this->_parseUrl($tag['value'], $tag['base']))
			$context['comments']['feed'] = $feed;
	}
}


/*********************************************************************
 * CreativeCommons
 */

class FeedParserExtensionCreativeCommons extends FeedParserHelper {
	
	function parseElementLicense(& $context, & $tag) {
		$link = array ();
		$link['rel'] = 'license';

		if (isset($tag['value']))
			$link['href'] = $this->_parseUrl($tag['value']);
			
		$context['links'][] = $link;
	}
}


/*********************************************************************
 * Thread
 */

class FeedParserExtensionThr extends FeedParserHelper {
	
	function parseAttributeCount(& $context, & $tag, & $attribute) {
		if ($context['rel'] == 'replies') {
			$context['thread']['count'] = $attribute['value'];
		}
	}
}


/*********************************************************************
 * Feedburner
 */

class FeedParserExtensionFeedburner extends FeedParserHelper {
	
	function parseElementOrigLink(& $context, & $tag) {
		$link = array ();
		$link['rel'] = 'alternate';

		if (isset($tag['value']))
			$link['href'] = $this->_parseUrl($tag['value']);
			
		$context['links'][] = $link;
	}
}
	
?>