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
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = preg_replace(
	'#({style_legend}.*);#U',
	'$1,additional_source;',
	$GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);
$GLOBALS['TL_DCA']['tl_layout']['fields']['additional_source'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['additional_source'],
	'inputType'               => 'checkbox',
	'options_callback'        => array('tl_layout_additional_source', 'getAdditionSources'),
	'eval'                    => array('multiple'=>true, 'tl_class'=>'clr')
);

/**
 * Class tl_layout_additional_source
 *
 */
class tl_layout_additional_source extends Backend
{
	public function getAdditionSources()
	{
		$arrAdditionalSource = array();
		$objAdditionalSource = $this->Database->prepare("
				SELECT
					s.*
				FROM
					`tl_additional_source` s
				INNER JOIN
					`tl_theme` t
				ON
					t.`id`=s.`pid`
				INNER JOIN
					`tl_layout` l
				ON
					t.`id` = l.`pid`
				WHERE
					l.`id`=?
				ORDER BY
					s.`sorting`")
		   ->execute($this->Input->get('id'));
		while ($objAdditionalSource->next())
		{
			$strType = $objAdditionalSource->type;
			$label = $objAdditionalSource->$strType;
			
			if (strlen($objAdditionalSource->cc)) {
				$label .= ' <span style="color: #B3B3B3;">[' . $objAdditionalSource->cc . ']</span>';
			}
			
			if (strlen($objAdditionalSource->media)) {
				$arrMedia = unserialize($objAdditionalSource->media);
				if (count($arrMedia)) {
					$label .= ' <span style="color: #B3B3B3;">[' . implode(', ', $arrMedia) . ']</span>';
				}
			}
			
			switch ($objAdditionalSource->type) {
			case 'js_file': case 'js_url':
				$image = 'iconJS.gif';
				break;
			
			case 'css_file': case 'css_url':
				$image = 'iconCSS.gif';
				break;
			
			default:
				$image = false;
				if (isset($GLOBALS['TL_HOOKS']['getAdditionalSourceIconImage']) && is_array($GLOBALS['TL_HOOKS']['getAdditionalSourceIconImage']))
				{
					foreach ($GLOBALS['TL_HOOKS']['getAdditionalSourceIconImage'] as $callback)
					{
						$this->import($callback[0]);
						$image = $this->$callback[0]->$callback[1]($row);
						if ($image !== false) {
							break;
						}
					}
				}
			}
			
			$arrAdditionalSource[$objAdditionalSource->id] = ($image ? $this->generateImage($image, $label, 'style="vertical-align:middle"') . ' ' : '') . $label;
		}
		return $arrAdditionalSource;
	}
}
?>