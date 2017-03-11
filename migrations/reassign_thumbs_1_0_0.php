<?php
/**
*
* @package phpBB Extension - Reassign Thumbs
* @copyright (c) 2017 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace sheer\reassign_thumbs\migrations;

class reassign_thumbs_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return;
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
		);
	}

	public function revert_schema()
	{
		return array(
		);
	}

	public function update_data()
	{
		return array(
			// Current version
			array('config.add', array('reassign_thumbs_version', '1.0.0')),
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_REASSIGN_THUMBS'
			)),
			array('module.add', array(
				'acp',
				'ACP_REASSIGN_THUMBS',
				array(
					'module_basename'	=> '\sheer\reassign_thumbs\acp\main_module',
					'modes'	=> array(
						'tools'
					),
				),
			)),
		);
	}
}
