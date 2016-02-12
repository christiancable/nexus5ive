<?php

namespace Nexus\Helpers;

class NxCodeHelper
{
	/**
	* converts a string with NXCode tags into 
	* markdown tags
	* @param $text string string with nxcode tags
	* @return string with markdown tags
	**/
	public static function NxToMarkdown($nxText)
	{
	    /*
	    nexus tags are
	    [www-][-www]
	    [i-][-i]
	    [b-][-b]
	    [picture-][-picture]
	    [youtube-][-youtube] @todo - add embed code
	    [ascii-][-ascii]

	    [u-][-u]
	    [small-][-small]
	    [quote-][-quote]
	    [updated-][-updated]
	    [hudson-][-hudson]
	    [spoiler-][-spoiler]

	    */

	    $nxTags = [
	    	'[www-]',
	    	'[-www]',
	    	'[i-]',
	    	'[-i]',
	    	'[b-]',
	    	'[-b]',
	    	'[picture-]',
	    	'[-picture]',
	    	'[youtube-]',
	    	'[-youtube]',
	    	'[ascii-]',
	    	'[-ascii]',
	    	'[quote-]',
	    	'[-quote]',
	    ];
	    $mdTags = [
	    	'',
	    	'',
	    	'_',
	    	'_',
	    	'__',
	    	'__',
	    	'![image](',
	    	')',
	    	'',
	    	'',
	    	'`',
	    	'`',
	    	'`',
	    	'`',
	    ];

	    $mdText = str_ireplace($nxTags, $mdTags, $nxText);

		return $mdText; 
	}
}