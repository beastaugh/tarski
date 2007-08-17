<?php

class entity {

	/* in:  utf-8 or us-ascii, containing entities (named or numeric)
	 * out: utf-8
	 */
	
	function decode ($string) {
		$string = entity::named_to_numeric($string);
		$string = entity::numeric_to_utf8($string);
		return $string;
	}


	/* in:  utf-8
	 * out: us-ascii, non-ascii characters encoded using numeric entities
	 */
	
	function encode($string) {
		$string = utf8::encode_numericentity($string);
		return $string;
	}
	
	
	/* in:  utf-8 or us-ascii, containing entities (named or numeric)
	 * out: utf-8 or us-ascii, containing numeric entities
	 */
	
	function normalize ($string, $type = 'xml') {
		$string = entity::named_to_numeric($string);
		$string = entity::normalize_numeric($string);
		$string = entity::specialchars($string, $type);
		return $string;
	}
	
	
	/* in:  utf-8 or us-ascii, containing some numeric or named entities
	   out: utf-8 or us-ascii, containing required entities and already existing entities
	*/
	
	function specialchars ($string, $type = 'xml') {
		$apos = $type == 'xml' ? '&apos;' : '&#39;';
		$specialchars = array (
			'&quot;'	=> '&quot;',		'&amp;'   	=> '&amp;',	  	
			'&apos;'  	=> $apos,			'&lt;'  	=> '&lt;',		
			'&gt;'    	=> '&gt;',			'"'			=> '&quot;',
			'&'			=> '&amp;',			"'"			=> $apos,
			'<'			=> '&lt;',			'>'			=> '&gt;'
		);
		
		$string = preg_replace('/&(#?[Xx]?[0-9A-Za-z]+);/', "[[[ENTITY:\\1]]]", $string);		
		$string = strtr($string, $specialchars);
		$string = preg_replace('/\[\[\[ENTITY\:([^\]]+)\]\]\]/', "&\\1;", $string);		
		return $string;
	}
	
	
	
	
	function named_to_numeric ($string) {
		$string = preg_replace('/(&[0-9A-Za-z]+)(;?\=?|([^A-Za-z0-9\;\:\.\-\_]))/e', "entity::_named('\\1', '\\2') . '\\3'", $string);
		return $string;	
	}
	
	function normalize_numeric ($string) {
		global $_entities;
		$string = preg_replace('/&#([0-9]+)(;)?/e', "'&#x'.dechex('\\1').';'", $string);
		$string = preg_replace('/&#[Xx](0)*([0-9A-Fa-f]+)(;?|([^A-Za-z0-9\;\:\.\-\_]))/e', "'&#x' . strtoupper('\\2') . ';\\4'", $string);
		$string = strtr($string, $_entities['cp1251']);
		return $string;
	}
 
	function numeric_to_utf8 ($string) {
		$string = preg_replace('/&#([0-9]+)(;)?/e', "'&#x'.dechex('\\1').';'", $string);
		$string = preg_replace('/&#[Xx](0)*([0-9A-Fa-f]+)(;?|([^A-Za-z0-9\;\:\.\-\_]))/e', "'&#x' . strtoupper('\\2') . ';\\4'", $string);
		$string = preg_replace('/&#x([0-9A-Fa-f]+);/e', "entity::_hex_to_utf8('\\1')", $string);		
		return $string; 	
	}
	
	function numeric_to_named ($string) {
		global $_entities;
		$string = preg_replace('/&#[Xx]([0-9A-Fa-f]+)/e', "'&#'.hexdec('\\1')", $string);
		$string = strtr($string, array_flip($_entities['named']));
		return $string;	
	}


	function _hex_to_utf8($s)
	{
		$c = hexdec($s);
		
		if ($c < 0x80) {
			$str = chr($c);
		}
		else if ($c < 0x800) {
			$str = chr(0xC0 | $c>>6) . chr(0x80 | $c & 0x3F);
		}
		else if ($c < 0x10000) {
			$str = chr(0xE0 | $c>>12) . chr(0x80 | $c>>6 & 0x3F) . chr(0x80 | $c & 0x3F);
		}
		else if ($c < 0x200000) {
			$str = chr(0xF0 | $c>>18) . chr(0x80 | $c>>12 & 0x3F) . chr(0x80 | $c>>6 & 0x3F) . chr(0x80 | $c & 0x3F);
		}
		
		return $str;
	} 		

	function _named($entity, $extra) {
		global $_entities;
		
		if ($extra == '=') return $entity . '=';
		
		$length = strlen($entity);
		
		while ($length > 0) {
			$check = substr($entity, 0, $length);
			if (isset($_entities['named'][$check])) return $_entities['named'][$check] . ';' . substr($entity, $length);
			$length--;
		}
		
		return $entity . ($extra == ';' ? ';' : '');
	}
}


$_entities['cp1251'] = array (
	'&#x80;' 		=> '&#x20AC;',	'&#x82;' 		=> '&#x201A;',	'&#x83;' 		=> '&#x192;',	
	'&#x84;' 		=> '&#x201E;',	'&#x85;' 		=> '&#x2026;',	'&#x86;' 		=> '&#x2020;',	
	'&#x87;' 		=> '&#x2021;',	'&#x88;' 		=> '&#x2C6;',	'&#x89;' 		=> '&#x2030;',	
	'&#x8A;' 		=> '&#x160;',	'&#x8B;' 		=> '&#x2039;',	'&#x8C;' 		=> '&#x152;',	
	'&#x8E;' 		=> '&#x17D;',	'&#x91;' 		=> '&#x2018;',	'&#x92;' 		=> '&#x2019;',	
	'&#x93;' 		=> '&#x201C;',	'&#x94;' 		=> '&#x201D;',	'&#x95;' 		=> '&#x2022;',	
	'&#x96;' 		=> '&#x2013;',	'&#x97;' 		=> '&#x2014;',	'&#x98;' 		=> '&#x2DC;',	
	'&#x99;' 		=> '&#x2122;',	'&#x9A;' 		=> '&#x161;',	'&#x9B;' 		=> '&#x203A;',	
	'&#x9C;' 		=> '&#x153;',	'&#x9E;' 		=> '&#x17E;',	'&#x9F;' 		=> '&#x178;',	
);
	
$_entities['named'] = array (
	'&nbsp' 		=> '&#160',		'&iexcl'		=> '&#161',		'&cent' 		=> '&#162',	
	'&pound' 		=> '&#163',		'&curren'		=> '&#164',		'&yen' 			=> '&#165',	
	'&brvbar'		=> '&#166', 	'&sect' 		=> '&#167',		'&uml' 			=> '&#168',	
	'&copy' 		=> '&#169',		'&ordf' 		=> '&#170',		'&laquo' 		=> '&#171',	
	'&not' 			=> '&#172',		'&shy' 			=> '&#173',		'&reg' 			=> '&#174',	
	'&macr' 		=> '&#175',		'&deg' 			=> '&#176',		'&plusmn' 		=> '&#177',	
	'&sup2' 		=> '&#178',		'&sup3' 		=> '&#179', 	'&acute' 		=> '&#180',	
	'&micro' 		=> '&#181', 	'&para' 		=> '&#182',		'&middot' 		=> '&#183',	
	'&cedil' 		=> '&#184', 	'&sup1' 		=> '&#185',		'&ordm' 		=> '&#186',	
	'&raquo' 		=> '&#187',		'&frac14' 		=> '&#188',		'&frac12' 		=> '&#189',	
	'&frac34' 		=> '&#190',		'&iquest' 		=> '&#191',		'&Agrave' 		=> '&#192',	
	'&Aacute' 		=> '&#193',		'&Acirc' 		=> '&#194',		'&Atilde' 		=> '&#195',	
	'&Auml' 		=> '&#196',		'&Aring' 		=> '&#197',		'&AElig' 		=> '&#198',	
	'&Ccedil'		=> '&#199', 	'&Egrave' 		=> '&#200',		'&Eacute' 		=> '&#201',	
	'&Ecirc' 		=> '&#202',		'&Euml' 		=> '&#203',		'&Igrave' 		=> '&#204',	
	'&Iacute' 		=> '&#205',		'&Icirc' 		=> '&#206',		'&Iuml' 		=> '&#207', 	
	'&ETH' 			=> '&#208',		'&Ntilde' 		=> '&#209',		'&Ograve' 		=> '&#210',	
	'&Oacute'		=> '&#211',		'&Ocirc' 		=> '&#212',		'&Otilde' 		=> '&#213',	
	'&Ouml' 		=> '&#214',		'&times' 		=> '&#215',		'&Oslash' 		=> '&#216',	
	'&Ugrave' 		=> '&#217',		'&Uacute' 		=> '&#218',		'&Ucirc' 		=> '&#219',	
	'&Uuml' 		=> '&#220',		'&Yacute' 		=> '&#221',		'&THORN' 		=> '&#222',	
	'&szlig' 		=> '&#223',		'&agrave' 		=> '&#224',		'&aacute' 		=> '&#225',	
	'&acirc' 		=> '&#226',		'&atilde' 		=> '&#227',		'&auml' 		=> '&#228',	
	'&aring' 		=> '&#229',		'&aelig' 		=> '&#230',		'&ccedil' 		=> '&#231',	
	'&egrave' 		=> '&#232',		'&eacute' 		=> '&#233',		'&ecirc' 		=> '&#234',	
	'&euml' 		=> '&#235',		'&igrave' 		=> '&#236',		'&iacute' 		=> '&#237',	
	'&icirc' 		=> '&#238',		'&iuml' 		=> '&#239',		'&eth' 			=> '&#240',	
	'&ntilde' 		=> '&#241',		'&ograve' 		=> '&#242',		'&oacute' 		=> '&#243',	
	'&ocirc' 		=> '&#244',		'&otilde' 		=> '&#245',		'&ouml' 		=> '&#246',	
	'&divide' 		=> '&#247',		'&oslash' 		=> '&#248',		'&ugrave' 		=> '&#249',	
	'&uacute' 		=> '&#250',		'&ucirc' 		=> '&#251',		'&uuml' 		=> '&#252',	
	'&yacute' 		=> '&#253',		'&thorn' 		=> '&#254',		'&yuml' 		=> '&#255',	
	'&OElig'		=> '&#338',		'&oelig'		=> '&#229',		'&Scaron'		=> '&#352',	
	'&scaron'		=> '&#353',		'&Yuml'			=> '&#376',		'&circ'			=> '&#710',	
	'&tilde'		=> '&#732', 	'&esnp'			=> '&#8194',	'&emsp'			=> '&#8195',	
	'&thinsp'		=> '&#8201',	'&zwnj'			=> '&#8204',	'&zwj'			=> '&#8205',	
	'&lrm'			=> '&#8206',	'&rlm'			=> '&#8207', 	'&ndash'		=> '&#8211', 	
	'&mdash'		=> '&#8212',	'&lsquo'		=> '&#8216',	'&rsquo'		=> '&#8217', 	
	'&sbquo'		=> '&#8218',	'&ldquo'		=> '&#8220', 	'&rdquo'		=> '&#8221',	
	'&bdquo'		=> '&#8222',	'&dagger'		=> '&#8224',	'&Dagger'		=> '&#8225',	
	'&permil'		=> '&#8240',	'&lsaquo'		=> '&#8249',	'&rsaquo'		=> '&#8250',
	'&euro'			=> '&#8364',	'&fnof'			=> '&#402',		'&Alpha'		=> '&#913',	
	'&Beta'			=> '&#914',		'&Gamma'		=> '&#915',		'&Delta'		=> '&#916',	
	'&Epsilon'		=> '&#917',		'&Zeta'			=> '&#918',		'&Eta'			=> '&#919',	
	'&Theta'		=> '&#920',		'&Iota'			=> '&#921',		'&Kappa'		=> '&#922',	
	'&Lambda'		=> '&#923',		'&Mu'			=> '&#924',		'&Nu'			=> '&#925',	
	'&Xi'			=> '&#926',		'&Omicron'		=> '&#927',		'&Pi'			=> '&#928',	
	'&Rho'			=> '&#929',		'&Sigma'		=> '&#931',		'&Tau'			=> '&#932',	
	'&Upsilon'		=> '&#933', 	'&Phi'			=> '&#934',		'&Chi'			=> '&#935',	
	'&Psi'			=> '&#936',		'&Omega'		=> '&#937',		'&alpha'		=> '&#945',	
	'&beta'			=> '&#946',		'&gamma'		=> '&#947',		'&delta'		=> '&#948',	
	'&epsilon'		=> '&#949',		'&zeta'			=> '&#950',		'&eta'			=> '&#951',	
	'&theta'		=> '&#952',		'&iota'			=> '&#953',		'&kappa'		=> '&#954',	
	'&lambda'		=> '&#955',		'&mu'			=> '&#956',		'&nu'			=> '&#957',	
	'&xi'			=> '&#958',		'&omicron'		=> '&#959',		'&pi'			=> '&#960',	
	'&rho'			=> '&#961',		'&sigmaf'		=> '&#962',		'&sigma'		=> '&#963',	
	'&tau'			=> '&#964',		'&upsilon'		=> '&#965', 	'&phi'			=> '&#966',	
	'&chi'			=> '&#967',		'&psi'			=> '&#968',		'&omega'		=> '&#969',	
	'&thetasym'		=> '&#977',		'&upsih'		=> '&#978',		'&piv'			=> '&#982',	
	'&bull'			=> '&#8226',	'&hellip'		=> '&#8230',	'&prime'		=> '&#8242',	
	'&Prime'		=> '&#8243',	'&oline'		=> '&#8254', 	'&frasl'		=> '&#8260',	
	'&weierp'		=> '&#8472', 	'&image'		=> '&#8465', 	'&real'			=> '&#8476',	
	'&trade'		=> '&#8482', 	'&alefsym' 		=> '&#8501', 	'&larr'			=> '&#8592', 	
	'&uarr'			=> '&#8593', 	'&rarr'			=> '&#8594',	'&darr'			=> '&#8595', 	
	'&harr'			=> '&#8596',	'&crarr'		=> '&#8629',	'&lArr'			=> '&#8656',	
	'&uArr'			=> '&#8657', 	'&rArr'			=> '&#8658', 	'&dArr'			=> '&#8659', 	
	'&hArr'			=> '&#8660', 	'&forall'		=> '&#8704', 	'&part'			=> '&#8706', 	
	'&exist'		=> '&#8707', 	'&empty'		=> '&#8709', 	'&nabla'		=> '&#8711', 	
	'&isin'			=> '&#8712', 	'&notin'		=> '&#8713', 	'&ni'			=> '&#8715', 	
	'&prod'			=> '&#8719', 	'&sum'			=> '&#8721', 	'&minus'		=> '&#8722', 	
	'&lowast'		=> '&#8727', 	'&radic'		=> '&#8730', 	'&prop'			=> '&#8733', 	
	'&infin'		=> '&#8734', 	'&ang'			=> '&#8736', 	'&and'			=> '&#8743', 	
	'&or'			=> '&#8744', 	'&cap'			=> '&#8745', 	'&cup'			=> '&#8746', 	
	'&int'			=> '&#8747', 	'&there4'		=> '&#8756', 	'&sim'			=> '&#8764', 	
	'&cong'			=> '&#8773', 	'&asymp'		=> '&#8776', 	'&ne'			=> '&#8800', 	
	'&equiv'		=> '&#8801', 	'&le'			=> '&#8804', 	'&ge'			=> '&#8805', 	
	'&sub'			=> '&#8834', 	'&sup'			=> '&#8835', 	'&nsub'			=> '&#8836', 	
	'&sube'			=> '&#8838', 	'&supe'			=> '&#8839', 	'&oplus'		=> '&#8853', 	
	'&otimes'  		=> '&#8855', 	'&perp'			=> '&#8869', 	'&sdot'			=> '&#8901', 	
	'&lceil'		=> '&#8968', 	'&rceil'		=> '&#8969', 	'&lfloor'		=> '&#8970', 	
	'&rfloor'		=> '&#8971', 	'&lang'			=> '&#9001', 	'&rang'			=> '&#9002', 	
	'&loz'			=> '&#9674', 	'&spades'		=> '&#9824', 	'&clubs'		=> '&#9827', 	
	'&hearts'		=> '&#9829', 	'&diams'		=> '&#9830', 	
);


?>