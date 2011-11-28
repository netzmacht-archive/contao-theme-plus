<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Theme+
 * Copyright (C) 2010,2011 InfinitySoft <http://www.infinitysoft.de>
 *
 * Extension for:
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
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
 * @copyright  2010,2011 InfinitySoft <http://www.infinitysoft.de>
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Theme+
 * @license    LGPL
 */


/**
 * Class FancyCheckBoxWizard
 *
 * Provide methods to handle sortable checkboxes.
 * @copyright  Tristan Lins 2011
 * @copyright  Leo Feyer 2005-2011
 * @author     Tristan Lins <http://www.infinitysoft.de>
 * @author     John Brand <http://www.thyon.com>
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Controller
 */
class FancyCheckBoxWizard extends CheckBoxWizard
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget_fancy_checkbox_wizard';


	/**
	 * Initialize the object
	 * @param array
	 * @throws Exception
	 */
	public function __construct($arrAttributes=false)
	{
		parent::__construct($arrAttributes);
		
		$arrData = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField];
		
		// !!! undo prepareForWidget shit !!!
		
		// Options callback
		if (is_array($arrData['options_callback']))
		{
			if (!is_object($arrData['options_callback'][0]))
			{
				$this->import($arrData['options_callback'][0]);
			}

			$arrData['options'] = $this->$arrData['options_callback'][0]->$arrData['options_callback'][1]($this);
		}
		
		// Add options
		$arrOptions = array();
		if (is_array($arrData['options']))
		{
			$blnIsAssociative = array_is_assoc($arrData['options']);
			$blnUseReference = isset($arrData['reference']);

			if ($arrData['eval']['includeBlankOption'])
			{
				$strLabel = strlen($arrData['eval']['blankOptionLabel']) ? $arrData['eval']['blankOptionLabel'] : '-';
				$arrOptions[] = array('value'=>'', 'label'=>$strLabel);
			}

			foreach ($arrData['options'] as $k=>$v)
			{
				if (!is_array($v))
				{
					$arrOptions[] = array('value'=>($blnIsAssociative ? $k : $v), 'label'=>($blnUseReference ? ((($ref = (is_array($arrData['reference'][$v]) ? $arrData['reference'][$v][0] : $arrData['reference'][$v])) != false) ? $ref : $v) : $v));
					continue;
				}
				
				if (isset($v['value']) && isset($v['label']))
				{
					if ($blnUseReference)
					{
						$v['label'] = (($ref = (is_array($arrData['reference'][$v['label']]) ? $arrData['reference'][$v['label']][0] : $arrData['reference'][$v['label']])) != false) ? $ref : $v['label'];
					}
					$arrOptions[] = $v;
					continue;
				}

				$key = $blnUseReference ? ((($ref = (is_array($arrData['reference'][$k]) ? $arrData['reference'][$k][0] : $arrData['reference'][$k])) != false) ? $ref : $k) : $k;
				$blnIsAssoc = array_is_assoc($v);

				foreach ($v as $kk=>$vv)
				{
					$arrOptions[$key][] = array('value'=>($blnIsAssoc ? $kk : $vv), 'label'=>($blnUseReference ? ((($ref = (is_array($arrData['reference'][$vv]) ? $arrData['reference'][$vv][0] : $arrData['reference'][$vv])) != false) ? $ref : $vv) : $vv));
				}
			}
		}
		
		// Mixin value callback
		if (is_array($arrData['mixin_value_callback']))
		{
			if (!is_object($arrData['mixin_value_callback'][0]))
			{
				$this->import($arrData['mixin_value_callback'][0]);
			}

			$arrData['mixin_value'] = $this->$arrData['mixin_value_callback'][0]->$arrData['mixin_value_callback'][1]($this);
		}
		
		// Add mixin value
		$this->mixinValue = $arrData['mixin_value'];
		
		$this->options = $arrOptions;
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$arrOptions = array();
		$arrDisabledCheckedOptions = array();
		$arrCheckedOptions = array();
		$arrUncheckedOptions = array();

		if (!is_array($this->varValue))
		{
			$this->varValue = array($this->varValue);
		}
		
		if (is_array($this->mixinValue))
		{
			$this->varValue = array_merge($this->mixinValue, $this->varValue);
		}

		foreach ($this->arrOptions as $arrOption)
		{
			if (!isset($arrOption['checked']))
			{
				$arrOption['checked']  = in_array($arrOption['value'], $this->varValue);
			}
			if (!isset($arrOption['disabled']))
			{
				$arrOption['disabled'] = false;
			}
			$arrOptions[$arrOption['value']] = $arrOption;
		}
		
		// sorted values
		$arrSort = array_keys($arrOptions);
		//ksort($arrOptions);
		
		// sort options by selected state and custom ordering
		if ($this->varValue)
		{
			// Move selected and sorted options to the top
			foreach ($arrOptions as $arrOption)
			{
				if (($intPos = array_search($arrOption['value'], $this->varValue)) !== false)
				{
					if ($arrOption['disabled'])
					{
						$arrDisabledCheckedOptions[$intPos] = $arrOption;
					}
					else
					{
						$arrCheckedOptions[$intPos] = $arrOption;
					}
				}
				else
				{
					$arrUncheckedOptions[] = $arrOption;
				}
			}
		}
		
		// sort by position
		ksort($arrDisabledCheckedOptions);
		ksort($arrCheckedOptions);

		$n = 1;
		// generate the options
		foreach ($arrDisabledCheckedOptions as $i=>$arrOption)
		{
			$arrDisabledCheckedOptions[$i] = $this->generateCheckbox($arrOption, $n++);
		}
		foreach ($arrCheckedOptions as $i=>$arrOption)
		{
			$arrCheckedOptions[$i] = $this->generateCheckbox($arrOption, $n++);
		}
		foreach ($arrUncheckedOptions as $i=>$arrOption)
		{
			$arrUncheckedOptions[$i] = $this->generateCheckbox($arrOption, $n++);
		}

        return sprintf('<fieldset id="ctrl_%s" class="tl_checkbox_container tl_fanzy_checkbox_wizard%s"><legend>%s%s%s%s</legend>%s<ul id="ctrl_%s_checked" class="sortable">%s</ul><hr><ul id="ctrl_%s_unchecked" class="sortable">%s</ul></fieldset>%s%s',
        				$this->strId,
						(($this->strClass != '') ? ' ' . $this->strClass : ''),
						($this->required ? '<span class="invisible">'.$GLOBALS['TL_LANG']['MSC']['mandatory'].'</span> ' : ''),
						$this->strLabel,
						($this->required ? '<span class="mandatory">*</span>' : ''),
						$this->xlabel,
						count($arrDisabledCheckedOptions) ? sprintf('<ul id="ctrl_%s_disabled_checked" class="sortable">%s</ul>', $this->strId, implode('', $arrDisabledCheckedOptions)) : '',
						$this->strId,
						count($arrOptions) ? implode('', $arrCheckedOptions) : '<p class="tl_noopt">'.$GLOBALS['TL_LANG']['MSC']['noResult'].'</p>',
						$this->strId,
						count($arrOptions) ? implode('', $arrUncheckedOptions) : '',
						$this->wizard,
						'<script>
(function() {
	var sorted = ' . json_encode($arrSort) . ';
	var sortable = new Sortables("ctrl_' . $this->strId . '_checked", { handle: "img.cut" });
	$$("#ctrl_' . $this->strId . ' ul li input[type=\'checkbox\']").each(function(e) {
		e.addEvent("change", function() {
			var li = $(this).getParent("li");
			if (this.checked) {
				li.getElement("img.cut").setStyle("display", "");
				li.getElement("img.cut_").setStyle("display", "none");
				li.inject("ctrl_' . $this->strId . '_checked");
				sortable.addItems(li);
			} else {
				li.getElement("img.cut").setStyle("display", "none");
				li.getElement("img.cut_").setStyle("display", "");
				sortable.removeItems(li);
				var items = $$("ul#ctrl_' . $this->strId . '_unchecked li input[type=\'checkbox\']");
				for (var i = sorted.indexOf(e.value.test(/^\d+/) ? parseInt(e.value) : e.value)+1; i<sorted.length; sorted++) {
					var item = $$("ul#ctrl_' . $this->strId . '_unchecked li input[value=\'" + sorted[i] + "\']");
					if (item.length) {
						li.inject(item[0].getParent("li"), "before");
						return;
					}
				}
				li.inject("ctrl_' . $this->strId . '_unchecked");
			}
		});
	});
})();
</script>');
	}


	/**
	 * Generate a checkbox and return it as string
	 * @param array
	 * @param integer
	 * @param string
	 * @return string
	 */
	protected function generateCheckbox($arrOption, $i)
	{
		return sprintf('<li style="cursor:auto;">%s%s&nbsp;<input type="checkbox" name="%s" id="opt_%s" class="tl_checkbox" value="%s"%s%s%s onfocus="Backend.getScrollOffset();"> <label for="opt_%s">%s</label></li>',
						$this->generateImage('cut_.gif', '', 'class="cut_" style="vertical-align: middle;' . ($arrOption['checked'] && !$arrOption['disabled'] ? ' display:none;' : '') . '"'),
						$this->generateImage('cut.gif', '', 'class="cut" style="vertical-align: middle; cursor: move;' . ($arrOption['checked'] && !$arrOption['disabled'] ? '' : ' display:none;') . '"'),
						$this->strName . ($this->multiple ? '[]' : ''),
						$this->strId.'_'.$i,
						($this->multiple ? specialchars($arrOption['value']) : 1),
						($arrOption['checked'] ? ' checked="checked"' : ''),
						$this->getAttributes(),
						($arrOption['disabled'] ? ' disabled="disabled"' : ''),
						$this->strId.'_'.$i,
						$arrOption['label']);
	}
}

?>