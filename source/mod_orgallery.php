<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_orslider
 *
 * @copyright   Copyright (C) 2019 - 2020 openROOM. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if ($params->def('prepare_content', 1))
{
	JPluginHelper::importPlugin('content');
	$module->content = JHtml::_('content.prepare', $module->content, '', 'mod_orgallery.content');
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

if (!function_exists('orGallery_dirFilter')) {
	function orGallery_dirFilter($url)
	{
		$tmp_dir = explode('/', $url);
		array_filter($tmp_dir);
		return implode('/', $tmp_dir);
	}
}
if (!function_exists('orGallery_listDirPhotos')) {
	function orGallery_listDirPhotos($dir, $maxDir, $addDir)
	{
		$arraySalida = array();
		$orGallery_list = scandir($dir);
		foreach($orGallery_list as $orGallery_listItem)
		{
			if($orGallery_listItem != "." && $orGallery_listItem != "..")
			{
				if(is_dir($dir.'/'.$orGallery_listItem) && $addDir)
				{
					if(is_dir($maxDir.'/'.$orGallery_listItem))
					{
						$tmp = orGallery_listDirPhotos($dir.'/'.$orGallery_listItem, $maxDir.'/'.$orGallery_listItem, $addDir);
						$arraySalida = array_merge($arraySalida,$tmp);
					}
				}
				else if(!is_dir($dir.'/'.$orGallery_listItem))
				{
					if(file_exists($dir.'/'.$orGallery_listItem) && file_exists($maxDir.'/'.$orGallery_listItem) && substr($orGallery_listItem,-4) != 'webp' && substr($orGallery_listItem,-2) != 'db')
					{
						$arraySalida[] = array('maxi' => $maxDir.'/'.$orGallery_listItem, 'mini' => $dir.'/'.$orGallery_listItem, 'title' => '');
					}
				}
			}
		}
		return $arraySalida;
	}
}

if (!function_exists('orTemplate_v4gallery')) {
	function orTemplate_v4gallery($version)
	{
		$rs = false; 
		$checkVersion = explode(".",$version);
		if($checkVersion[0] >= 1 &&
		   $checkVersion[1] >= 17 &&
		   ( (isset($checkVersion[2]) && $checkVersion[2] >= 0) || (!isset($checkVersion[2]) && $checkVersion[1] >= 18)  ) )
		{ $rs = true; }
		return $rs;
	}
}
require JModuleHelper::getLayoutPath('mod_orgallery', $params->get('layout', 'default'));
