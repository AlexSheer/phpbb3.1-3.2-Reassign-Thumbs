<?php
/**
*
* @package phpBB Extension - Reassign Thumbs
* @copyright (c) 2017 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace sheer\reassign_thumbs\acp;

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $request, $user, $template, $phpbb_root_path, $phpEx;

		$this->tpl_name = 'acp_main';
		$this->page_title = $user->lang['ACP_REASSIGN_THUMBS'];

		@set_time_limit(1200);

		$submit		= $request->variable('submit', false);
		$start		= $request->variable('start', 0);
		$cycle		= $request->variable('limit', 20);
		$done		= $request->variable('done', 0);

		if ($submit)
		{
			$this->rebuild_thumbs($start, $cycle, $done);
		}

		$template->assign_vars(array(
			'S_REASSIGN_THUMBS_ACTION'	=> $this->u_action,
			'CYCLE'						=> $cycle,
		));
	}

	function rebuild_thumbs($start, $limit, $done)
	{
		global $db, $config, $phpbb_root_path, $phpEx, $user;

		include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

		$end = $start + $limit;

		$output = array();
		$upload_path = $phpbb_root_path . $config['upload_path'] . '/';

		$total = $this->count_total_images();
		$images = $this->get_images($start, $limit);

		if (!$images && $start > 0)
		{
			$log_file = $upload_path . 'log.txt';
			$fp = @fopen($log_file, 'r');
			$contents = @fread($fp, filesize($log_file));
			$contents = substr($contents, 0, -1);
			@fclose($fp);
			@unlink($log_file);
			if ($contents)
			{
				$sql = 'UPDATE ' . ATTACHMENTS_TABLE . ' SET thumbnail = 1 WHERE attach_id IN (' . $contents . ')';
				$db->sql_query($sql);
			}
			trigger_error($user->lang['REBUILD_THUMBNAILS_COMPLETE'] . adm_back_link(append_sid("{$phpbb_root_path}adm/index.$phpEx", "i=-sheer-reassign_thumbs-acp-main_module")));
		}
		else if (!$images && $start == 0)
		{
			trigger_error($user->lang['NO_THUMBNAILS_TO_REBUILD'] . adm_back_link(append_sid("{$phpbb_root_path}adm/index.$phpEx", "i=-sheer-reassign_thumbs-acp-main_module")), E_USER_WARNING);
		}
		for ($i = 0; $i < count($images); $i++)
		{
			$source_file = $upload_path . $images[$i]['physical_filename'];
			//Generate Thumbnail Filename
			$thumb_file_name = 'thumb_' . $images[$i]['physical_filename'];
			//Make Sure The File Actually Exists Before Processing It
			if (file_exists($upload_path . $images[$i]['physical_filename']))
			{
				if (create_thumbnail($upload_path . $images[$i]['physical_filename'], $upload_path . $thumb_file_name, $images[$i]['mimetype']))
				{
					$output[] = $user->lang['REBUILT'] . $images[$i]['physical_filename'] . ' ' . $user->lang['THUMB'] . ' '. $thumb_file_name;
					$thumbs[] = $images[$i]['attach_id'];
				}
				else
				{
					$output[] = $user->lang['NO_NEED_REBUILT'] . $images[$i]['physical_filename'];
				}
			}
			else
			{
				$output[] = $user->lang['SOURCE_UNAVAILABLE'] . $images[$i]['physical_filename'];
			}
			$done++;
		}
		if (!isset($thumbs))
		{
			$thumbs = array();
		}
		$this->write_logfile ($upload_path, $thumbs);
		//Add The Status Message
		trigger_error('<meta http-equiv="refresh" content="3;url=' . append_sid("{$phpbb_root_path}adm/index.$phpEx", "i=-sheer-reassign_thumbs-acp-main_module&amp;start=$end&amp;limit=$limit&amp;submit=1&amp;done=$done") . '">'."<div align=\"left\"><strong>" . $user->lang['NEED_TO_PROCESS'] . "$total<br />". $user->lang['ENDED_AT'] .  count($output) . "<br />" . $user->lang['HAVE_DONE'] . "$done <br /></strong>" . implode("<br />", $output) . adm_back_link(append_sid("{$phpbb_root_path}adm/index.$phpEx", "i=-sheer-reassign_thumbs-acp-main_module")));
	}

	function write_logfile ($upload_path, $output)
	{
		$log_handle = @fopen($upload_path . 'log.txt', 'a+');
		$data = implode(',', $output);
		$data .= ',';
		$fp = @fopen($upload_path . 'log.txt', 'a+');
		@fwrite($fp, $data);
		@fclose($fp);
	}

	function count_total_images()
	{
		global $db, $config;

		$sql = 'SELECT extension
			FROM ' . EXTENSIONS_TABLE . '
			WHERE group_id = (SELECT group_id FROM ' . EXTENSION_GROUPS_TABLE . ' WHERE group_name = \'IMAGES\')';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$extensions[] = $row['extension'];
		}
		$db->sql_freeresult($result);

		$sql_where = '';
		foreach($extensions as $extension)
		{
			$sql_where .= 'extension = \'' . $extension . '\' OR ';
		}

		$sql_where = substr($sql_where, 0, -4);

		$sql = 'SELECT COUNT(attach_id) AS total
			FROM ' . ATTACHMENTS_TABLE . '
			 WHERE thumbnail = 0 AND filesize > ' . $config['img_min_thumb_filesize'] . ' AND (' . $sql_where . ')';
		$result = $db->sql_query($sql);
		$total = (int) $db->sql_fetchfield('total');
		$db->sql_freeresult($result);
		return $total;
	}

	function get_images($start = 0 , $limit = 20)
	{
		global $config, $db;

		$data = array();

		$sql = 'SELECT extension
			FROM ' . EXTENSIONS_TABLE . '
			WHERE group_id = (SELECT group_id FROM ' . EXTENSION_GROUPS_TABLE . ' WHERE group_name = \'IMAGES\')';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$extensions[] = $row['extension'];
		}
		$db->sql_freeresult($result);

		$sql_where = '';
		foreach($extensions as $extension)
		{
			$sql_where .= 'extension = \'' . $extension . '\' OR ';
		}

		$sql_where = substr($sql_where, 0, -4);

		$sql = 'SELECT attach_id, physical_filename, mimetype
			FROM ' . ATTACHMENTS_TABLE . '
			 WHERE thumbnail = 0 AND filesize > ' . $config['img_min_thumb_filesize'] . ' AND (' . $sql_where . ')';

		$result = $db->sql_query_limit($sql, $limit, $start);
		while ($row = $db->sql_fetchrow($result))
		{
			$data[] = $row;
		}
		$db->sql_freeresult($result);

		return $data;
	}
}
