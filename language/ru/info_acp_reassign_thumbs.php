<?php
/**
*
* @package phpBB Extension - Reassign Thumbs
* @copyright (c) 2017 Sheer
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_REASSIGN_THUMBS'			=> 'Пересоздание миниатюр',
	'ACP_REASSIGN_THUMBS_TOOL'		=> 'Пересоздание миниатюр',
	'TOOLS_IMAGES_PER_CYCLE'		=> 'Количество вложений, обрабатываемых за цикл',
	'NO_THUMBNAILS_TO_REBUILD'		=> 'Нет файлов для которых требуется создание миниатюр',
	'REBUILD_THUMBNAILS_COMPLETE'	=> 'Создание миниатюр завершено',
	'SOURCE_UNAVAILABLE'			=> 'Файл не найден',
	'REBUILT'						=> '<strong>Создано для</strong> ',
	'NO_NEED_REBUILT'				=> '<strong style="color: #aaa;">Не требуется</strong> создание миниатюры для файла ',
	'HAVE_DONE' 					=> 'Обработано всего: ',
	'NEED_TO_PROCESS' 				=> 'Найдено файлов без миниатюр: ',
	'THUMB'							=> '<strong>миниатюра</strong>',
	'REBUILT_THUMB'					=> 'Создать миниатюры',
	'ENDED_AT' 						=> 'Обработано за текущий шаг: ',
));
