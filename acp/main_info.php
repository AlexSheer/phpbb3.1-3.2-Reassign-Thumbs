<?php
/**
*
* @package phpBB Extension - Reassign Thumbs
* @copyright (c) 2017 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\reassign_thumbs\acp;

class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\sheer\reassign_thumbs\acp\main_module',
			'title'			=> 'ACP_REASSIGN_THUMBS',
			'version'		=> '1.0.0',
			'modes'			=> array(
				'tools'		=> array('title' => 'ACP_REASSIGN_THUMBS_TOOL',
				'auth' 		=> 'ext_sheer/reassign_thumbs && acl_a_board',
				'cat' 		=> array('ACP_REASSIGN_THUMBS')
				),
			),
		);
	}
}
