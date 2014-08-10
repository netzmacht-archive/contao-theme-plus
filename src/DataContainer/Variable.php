<?php

/**
 * Theme+ - Theme extension for the Contao Open Source CMS
 *
 * Copyright (C) 2013 bit3 UG <http://bit3.de>
 *
 * @package    Theme+
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @link       http://www.themeplus.de
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

namespace Bit3\Contao\ThemePlus\DataContainer;

use Bit3\Contao\ThemePlus\Model\VariableModel;
use Bit3\Contao\ThemePlus\ThemePlus;

/**
 * Class Stylesheet
 */
class Variable
{
	/**
	 * Get the variable name.
	 */
	static public function getName($varValue, $dc)
	{
		$varValue = standardize($varValue);

		$objVariable = \Database::getInstance()
			->prepare("SELECT * FROM tl_theme_plus_variable WHERE id!=? AND pid=? AND name=?")
			->execute($dc->id, $dc->activeRecord->pid, $varValue);
		if ($objVariable->next()) {
			throw new Exception(
				sprintf(
					$GLOBALS['TL_LANG']['ERR']['unique'],
					$GLOBALS['TL_LANG']['tl_theme_plus_variable']['name'][0]
				)
			);
		}

		return $varValue;
	}

	/**
	 * List an variable
	 *
	 * @param array
	 *
	 * @return string
	 */
	static public function listVariables($row)
	{
		$variable = VariableModel::findByPk($row['id']);

		$label = '<strong>' . $variable->name . '</strong>: ' . ThemePlus::renderVariable($variable);

		switch ($variable->type) {
			case 'text':
				$image = 'assets/theme-plus/images/text.png';
				break;

			case 'url':
				$image = 'assets/theme-plus/images/url.png';
				break;

			case 'file':
				$image = 'files.gif';
				break;

			case 'color':
				$image = 'assets/theme-plus/images/color.png';
				break;

			case 'size':
				$image = 'assets/theme-plus/images/size.png';
				break;

			default:
				$image = '';
		}

		if ($image) {
			$image = \Image::getHtml(
					$image,
					$GLOBALS['TL_LANG']['tl_theme_plus_variable'][$variable->type][0],
					'style="vertical-align:middle" title="' . specialchars(
						$GLOBALS['TL_LANG']['tl_theme_plus_variable'][$variable->type][0]
					) . '"'
				)
				. ' ';
		}

		return '<div>' . $image . $label . "</div>\n";
	}
}
