<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  InfinitySoft 2010
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class LayoutAdditionalSources extends Frontend {
	public function __construct() {
		$this->import('Database');
	}
	
	public function generatePage(Database_Result $objPage, $objLayout, $objPageRegular) {
		$strAdditionalJavaScript = '';
		$strAdditionalCSS = '';
		$strAdditionalOther = '';
		
		$objAdditionalSources = $this->Database->prepare("SELECT * FROM tl_additional_source WHERE pid=? ORDER BY sorting")
											   ->execute($objLayout->pid);
		while ($objAdditionalSources->next()) {
			if ($objAdditionalSources->restrictLayout) {
				$arrLayouts = unserialize($objAdditionalSources->layout);
				if (!in_array($objLayout->id, $arrLayouts)) {
					continue;
				}
			}
			
			$strAdditionalSource = '';
			switch ($objAdditionalSources->type) {
			case 'js_file':
			case 'js_url':
				$type = $objAdditionalSources->type;
				$strAdditionalSource = '<script type="text/javascript" src="'.$objAdditionalSources->$type.'"></script>';
				break;
				
			case 'css_file':
			case 'css_url':
				$type = $objAdditionalSources->type;
				$media = unserialize($objAdditionalSources->media);
				if (is_array($media) && count($media)) {
					$media = ' media="'.implode(',', $media).'"';
				} else {
					$media = '';
				}
				$strAdditionalSource = '<link type="text/css" rel="stylesheet" href="'.$objAdditionalSources->$type.'"'.$media.' />';
				break;
				
			default:
				if (isset($GLOBALS['TL_HOOKS']['generateAdditionalSource']) && is_array($GLOBALS['TL_HOOKS']['generateAdditionalSource']))
				{
					foreach ($GLOBALS['TL_HOOKS']['generateAdditionalSource'] as $callback)
					{
						$this->import($callback[0]);
						$strAdditionalSource = $this->$callback[0]->$callback[1]($objAdditionalSources, $objPage, $objLayout);
						if ($strAdditionalSource !== false)
						{
							break;
						}
					}
				}
			}
			
			if (!strlen($strAdditionalSource)) {
				continue;
			}
			
			if (strlen($objAdditionalSources->cc)) {
				$strAdditionalSource = '<!--[' . $objAdditionalSources->cc . ']>' . $strAdditionalSource . '<![endif]-->';
			}
		
			switch ($objAdditionalSources->type) {
			case 'js_file':
			case 'js_url':
				$strAdditionalJavaScript .= $strAdditionalSource."\n";
				break;
				
			case 'css_file':
			case 'css_url':
				$strAdditionalCSS .= $strAdditionalSource."\n";
				break;
			
			default:
				$strAdditionalOther .= $strAdditionalSource."\n";
				break;
			}
		}
		
		$objPageRegular->Template->additionalJavaScript = $strAdditionalJavaScript;
		$objPageRegular->Template->additionalCSS = $strAdditionalCSS;
		$objPageRegular->Template->additionalOther = $strAdditionalOther;
	}
}

?>