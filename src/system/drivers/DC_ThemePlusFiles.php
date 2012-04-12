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


require(TL_ROOT . '/system/drivers/DC_Table.php');

$GLOBALS['TL_CSS']['theme_plus'] = 'system/modules/theme_plus/html/be.css';


/**
 * Class DC_ThemePlusFilesTable
 *
 * @copyright  2010,2011 InfinitySoft <http://www.infinitysoft.de>
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Theme+
 */
class DC_ThemePlusFiles extends DC_Table
{
	/**
	 * Initialize the object
	 *
	 * @param string
	 */
	public function __construct($strTable)
	{
		$this->import('Input');
		if (preg_match('#^(\d+)_[0-9a-f]+$#', $this->Input->get('pid'), $match)) {
			$this->Input->setGet('pid', $match[1]);
		}

		parent::__construct($strTable);
	}

	protected function groupByType($arrIds)
	{
		$arrFiles = array();
		$objFile  = $this->Database
			->query('SELECT * FROM tl_theme_plus_file WHERE id IN (' . implode(',', $arrIds) . ')');
		while ($objFile->next()) {
			$arrFiles[preg_replace('#^(css|js)_.*$#', '$1', $objFile->type)][] = $objFile->id;
		}
		return $arrFiles;
	}

	protected function getLastRecord($strType)
	{
		$objFile = $this->Database
			->prepare('SELECT * FROM tl_theme_plus_file WHERE type LIKE ? ORDER BY sorting DESC LIMIT 1')
			->execute($strType . '_%');
		if ($objFile->next()) {
			return $objFile->id;
		}
		return false;
	}

	/**
	 * Move all selected records
	 */
	public function cutAll()
	{
		// PID is mandatory
		if (!strlen($this->Input->get('pid'))) {
			$this->redirect($this->getReferer());
		}

		$arrClipboard = $this->Session->get('CLIPBOARD');
		$blnBottom    = $this->Input->get('mode') == -2;

		if (isset($arrClipboard[$this->strTable]) && is_array($arrClipboard[$this->strTable]['id'])) {
			$arrFiles = $this->groupByType($arrClipboard[$this->strTable]['id']);

			foreach ($arrFiles as $strType => $arrIds)
			{
				if ($blnBottom) {
					$this->Input->setGet('pid', $this->getLastRecord($strType));
					$this->Input->setGet('mode', 1);
				}
				else {
					$this->Input->setGet('mode', 2);
				}

				foreach ($arrIds as $id) {
					$this->intId = $id;
					$this->cut(true);
					$this->Input->setGet('pid', $id);
					$this->Input->setGet('mode', 1);
				}
			}
		}

		$this->redirect($this->getReferer());
	}

	/**
	 * Copy all selected records
	 */
	public function copyAll()
	{
		// PID is mandatory
		if (!strlen($this->Input->get('pid'))) {
			$this->redirect($this->getReferer());
		}

		$arrClipboard = $this->Session->get('CLIPBOARD');
		$blnBottom    = $this->Input->get('mode') == -2;

		if (isset($arrClipboard[$this->strTable]) && is_array($arrClipboard[$this->strTable]['id'])) {
			$arrFiles = $this->groupByType($arrClipboard[$this->strTable]['id']);

			foreach ($arrFiles as $strType=> $arrIds)
			{
				if ($blnBottom) {
					$this->Input->setGet('pid', $this->getLastRecord($strType));
					$this->Input->setGet('mode', 1);
				}
				else {
					$this->Input->setGet('mode', 2);
				}

				foreach ($arrIds as $id) {
					$this->intId = $id;
					$this->copy(true);
					$this->Input->setGet('pid', $id);
					$this->Input->setGet('mode', 1);
				}
			}
		}

		$this->redirect($this->getReferer());
	}

	/**
	 * Show header of the parent table and list all records of the current table
	 * @return string
	 */
	protected function parentView()
	{
		$blnClipboard  = false;
		$arrClipboard  = $this->Session->get('CLIPBOARD');
		$table         = ($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['mode'] == 6) ? $this->ptable : $this->strTable;
		$blnHasSorting = $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['fields'][0] == 'sorting';
		$blnMultiboard = false;

		if ($this->Input->get('act') == 'paste') {
			switch ($this->Input->get('mode')) {
				case 'create_css':
					$this->Session->set('THEME_PLUS_FILE_TYPE', 'css');
					$this->redirect($this->addToUrl('mode=create'));

				case 'create_js':
					$this->Session->set('THEME_PLUS_FILE_TYPE', 'js');
					$this->redirect($this->addToUrl('mode=create'));

				default:
					if (!$this->Session->get('THEME_PLUS_FILE_TYPE')) {
						$this->redirect('contao/main.php?do=themes&table=tl_theme_plus_file&id=' . $this->Input->get('id'));
					}
			}
		}

		// Check clipboard
		if (!empty($arrClipboard[$table])) {
			$arrClipboard = $arrClipboard[$table];

			if ($this->Input->get('act') != 'select') {
				if ($arrClipboard['mode'] == 'create') {
					$blnClipboard       = true;
					$blnGlobalClipboard = true;
				}
				else {
					// get clipboard files ids
					$strIds = is_array($arrClipboard['id']) ? implode(',', $arrClipboard['id']) : $arrClipboard['id'];
					// get types of clipboard files
					$arrTypes = $this->Database
						->query('SELECT DISTINCT IF(type LIKE \'css_%\', \'css\', \'js\') AS type FROM tl_theme_plus_file WHERE id IN (' . $strIds . ')')
						->fetchEach('type');
					// only files of one type are in the clipboard
					if (count($arrTypes) == 1) {
						$this->Session->set('THEME_PLUS_FILE_TYPE', $arrTypes[0]);
						$blnClipboard        = true;
						$blnGlobalClipboard  = true;
						$blnGlobalMultiboard = false;
					}
					// files of both types are in the clipboard
					else {
						$this->Session->set('THEME_PLUS_FILE_TYPE', '');
						$blnClipboard        = false;
						$blnGlobalClipboard  = true;
						$blnGlobalMultiboard = true;
					}
				}

				if (is_array($arrClipboard['id'])) {
					$blnMultiboard = true;
				}
			}
		}

		// Load language file and data container array of the parent table
		$this->loadLanguageFile($this->ptable);
		$this->loadDataContainer($this->ptable);

		$return = '
<div id="tl_buttons">
<a href="' . $this->getReferer(true, $this->ptable) . '" class="header_back" title="' . specialchars($GLOBALS['TL_LANG']['MSC']['backBT']) . '" accesskey="b" onclick="Backend.getScrollOffset()">' . $GLOBALS['TL_LANG']['MSC']['backBT'] . '</a>' . (($this->Input->get('act') != 'select') ? ' &#160; :: &#160; ' .
			(!$GLOBALS['TL_DCA'][$this->strTable]['config']['closed'] ? '
<a href="' . $this->addToUrl(($blnHasSorting ? 'act=paste&amp;mode=create_css' : 'act=create_css&amp;mode=2&amp;pid=' . $this->intId)) . '" class="header_new_css" title="' . specialchars($GLOBALS['TL_LANG'][$this->strTable]['new_css'][1]) . '" accesskey="n" onclick="Backend.getScrollOffset()">' . $GLOBALS['TL_LANG'][$this->strTable]['new_css'][0] . '</a>' . '
 &nbsp; :: &nbsp;
<a href="' . $this->addToUrl(($blnHasSorting ? 'act=paste&amp;mode=create_js' : 'act=create_js&amp;mode=2&amp;pid=' . $this->intId)) . '" class="header_new_js" title="' . specialchars($GLOBALS['TL_LANG'][$this->strTable]['new_js'][1]) . '" accesskey="n" onclick="Backend.getScrollOffset()">' . $GLOBALS['TL_LANG'][$this->strTable]['new_js'][0] . '</a>'
				: '') .
			$this->generateGlobalButtons() .
			($blnClipboard ? ' &nbsp; :: &nbsp; <a href="' . $this->addToUrl('clipboard=1') . '" class="header_clipboard" title="' . specialchars($GLOBALS['TL_LANG']['MSC']['clearClipboard']) . '" accesskey="x">' . $GLOBALS['TL_LANG']['MSC']['clearClipboard'] . '</a>' : '') : '') . '
</div>' . $this->getMessages(true);

		// Get all details of the parent record
		$objParent = $this->Database->prepare("SELECT * FROM " . $this->ptable . " WHERE id=?")
			->limit(1)
			->execute(CURRENT_ID);

		if ($objParent->numRows < 1) {
			return $return;
		}

		$return .= (($this->Input->get('act') == 'select') ? '

<form action="' . ampersand($this->Environment->request, true) . '" id="tl_select" class="tl_form" method="post">
<div class="tl_formbody">
<input type="hidden" name="FORM_SUBMIT" value="tl_select">
<input type="hidden" name="REQUEST_TOKEN" value="' . REQUEST_TOKEN . '">' : '') . '

<div class="tl_listing_container parent_view">

<div class="tl_header" onmouseover="Theme.hoverDiv(this,1)" onmouseout="Theme.hoverDiv(this,0)">';

		// List all records of the child table
		if (!$this->Input->get('act') || $this->Input->get('act') == 'paste' || $this->Input->get('act') == 'select') {
			// Header
			$imagePasteNew    = $this->generateImage('new.gif', $GLOBALS['TL_LANG'][$this->strTable]['pasteafter'][0]);
			$imagePasteTop    = $this->generateImage('pasteafter.gif', $GLOBALS['TL_LANG'][$this->strTable]['pasteafter'][0], 'class="blink"');
			$imagePasteBottom = $this->generateImage('pasteinto.gif', $GLOBALS['TL_LANG'][$this->strTable]['pasteinto'][0], 'class="blink"');
			$imageEditHeader  = $this->generateImage('edit.gif', $GLOBALS['TL_LANG'][$this->strTable]['editheader'][0]);

			$return .= '
<div class="tl_content_right">';

			if ($this->Input->get('act') == 'select') {
				$return .= '
<label for="tl_select_trigger" class="tl_select_label">' . $GLOBALS['TL_LANG']['MSC']['selectAll'] . '</label> <input type="checkbox" id="tl_select_trigger" onclick="Backend.toggleCheckboxes(this)" class="tl_tree_checkbox">';
			}
			else if (!$GLOBALS['TL_DCA'][$this->ptable]['config']['notEditable']) {
				$return .= '
<a href="' . preg_replace('/&(amp;)?table=[^& ]*/i', (strlen($this->ptable) ? '&amp;table=' . $this->ptable : ''), $this->addToUrl('act=edit')) . '" title="' . specialchars($GLOBALS['TL_LANG'][$this->strTable]['editheader'][1]) . '">' . $imageEditHeader . '</a>';
			}
			if (($blnHasSorting && !$GLOBALS['TL_DCA'][$this->strTable]['config']['closed'])) {
				$return .= ' <a href="' . $this->addToUrl('act=create&amp;mode=2&amp;pid=' . $objParent->id . '&amp;id=' . $this->intId) . '" title="' . specialchars($GLOBALS['TL_LANG'][$this->strTable]['pastenew'][0]) . '">' . $imagePasteNew . '</a>';
			}
			if ($blnGlobalClipboard) {
				$return .= ' <a href="' . $this->addToUrl('act=' . $arrClipboard['mode'] . '&amp;mode=2&amp;pid=' . $objParent->id . (!$blnMultiboard ? '&amp;id=' . $arrClipboard['id'] : '')) . '" title="' . specialchars($GLOBALS['TL_LANG'][$this->strTable]['pastetop'][0]) . '" onclick="Backend.getScrollOffset()">' . $imagePasteTop . '</a>';
			}
			if ($blnGlobalMultiboard) {
				$return .= ' <a href="' . $this->addToUrl('act=' . $arrClipboard['mode'] . '&amp;mode=-2&amp;pid=' . $objParent->id . (!$blnMultiboard ? '&amp;id=' . $arrClipboard['id'] : '')) . '" title="' . specialchars($GLOBALS['TL_LANG'][$this->strTable]['pastebottom'][0]) . '" onclick="Backend.getScrollOffset()">' . $imagePasteBottom . '</a>';
			}

			$return .= '
</div>';

			// Format header fields
			$add          = array();
			$headerFields = $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['headerFields'];

			foreach ($headerFields as $v)
			{
				$_v = deserialize($objParent->$v);

				if (is_array($_v)) {
					$_v = implode(', ', $_v);
				}
				elseif ($GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['inputType'] == 'checkbox' && !$GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['eval']['multiple'])
				{
					$_v = strlen($_v) ? $GLOBALS['TL_LANG']['MSC']['yes'] : $GLOBALS['TL_LANG']['MSC']['no'];
				}
				elseif ($_v && $GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['eval']['rgxp'] == 'date')
				{
					$_v = $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $_v);
				}
				elseif ($_v && $GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['eval']['rgxp'] == 'time')
				{
					$_v = $this->parseDate($GLOBALS['TL_CONFIG']['timeFormat'], $_v);
				}
				elseif ($_v && $GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['eval']['rgxp'] == 'datim')
				{
					$_v = $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], $_v);
				}
				elseif ($v == 'tstamp')
				{
					$objMaxTstamp = $this->Database->prepare("SELECT MAX(tstamp) AS tstamp FROM " . $this->strTable . " WHERE pid=?")
						->execute($objParent->id);

					if (!$objMaxTstamp->tstamp) {
						$objMaxTstamp->tstamp = $objParent->tstamp;
					}

					$_v = $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'], max($objParent->tstamp, $objMaxTstamp->tstamp));
				}
				elseif (isset($GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['foreignKey']))
				{
					$arrForeignKey = explode('.', $GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['foreignKey'], 2);

					$objLabel = $this->Database->prepare("SELECT " . $arrForeignKey[1] . " AS value FROM " . $arrForeignKey[0] . " WHERE id=?")
						->limit(1)
						->execute($_v);

					if ($objLabel->numRows) {
						$_v = $objLabel->value;
					}
				}
				elseif (is_array($GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['reference'][$_v]))
				{
					$_v = $GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['reference'][$_v][0];
				}
				elseif (isset($GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['reference'][$_v]))
				{
					$_v = $GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['reference'][$_v];
				}
				elseif ($GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['eval']['isAssociative'] || array_is_assoc($GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['options']))
				{
					$_v = $GLOBALS['TL_DCA'][$this->ptable]['fields'][$v]['options'][$_v];
				}

				// Add the sorting field
				if ($_v != '') {
					$key       = isset($GLOBALS['TL_LANG'][$this->ptable][$v][0]) ? $GLOBALS['TL_LANG'][$this->ptable][$v][0] : $v;
					$add[$key] = $_v;
				}
			}

			// Trigger the header_callback (see #3417)
			if (is_array($GLOBALS['TL_DCA'][$table]['list']['sorting']['header_callback'])) {
				$strClass  = $GLOBALS['TL_DCA'][$table]['list']['sorting']['header_callback'][0];
				$strMethod = $GLOBALS['TL_DCA'][$table]['list']['sorting']['header_callback'][1];

				$this->import($strClass);
				$add = $this->$strClass->$strMethod($add, $this);
			}

			// Output the header data
			$return .= '

<table class="tl_header_table">';

			foreach ($add as $k=> $v)
			{
				if (is_array($v)) {
					$v = $v[0];
				}

				$return .= '
  <tr>
    <td><span class="tl_label">' . $k . ':</span> </td>
    <td>' . $v . '</td>
  </tr>';
			}

			$return .= '
</table>
</div>';

			$orderBy      = array();
			$firstOrderBy = array();

			// Add all records of the current table
			$query = "SELECT * FROM " . $this->strTable;

			if (is_array($this->orderBy) && strlen($this->orderBy[0])) {
				$orderBy      = $this->orderBy;
				$firstOrderBy = preg_replace('/\s+.*$/i', '', $orderBy[0]);

				// Order by the foreign key
				if (isset($GLOBALS['TL_DCA'][$this->strTable]['fields'][$firstOrderBy]['foreignKey'])) {
					$key        = explode('.', $GLOBALS['TL_DCA'][$this->strTable]['fields'][$firstOrderBy]['foreignKey'], 2);
					$query      = "SELECT *, (SELECT " . $key[1] . " FROM " . $key[0] . " WHERE " . $this->strTable . "." . $firstOrderBy . "=" . $key[0] . ".id) AS foreignKey FROM " . $this->strTable;
					$orderBy[0] = 'foreignKey';
				}
			}
			elseif (is_array($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['fields']))
			{
				$orderBy      = $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['fields'];
				$firstOrderBy = preg_replace('/\s+.*$/i', '', $orderBy[0]);
			}

			if (!empty($this->procedure)) {
				$query .= " WHERE " . implode(' AND ', $this->procedure);
			}

			if (is_array($this->root) && !empty($this->root)) {
				$query .= (!empty($this->procedure) ? " AND " : " WHERE ") . "id IN(" . implode(',', array_map('intval', $this->root)) . ")";
			}

			// Sort by type
			array_unshift($orderBy, 'IF(type LIKE \'css_%\', 0, 1)');

			if (is_array($orderBy) && !empty($orderBy)) {
				$query .= " ORDER BY " . implode(', ', $orderBy);
			}

			$objOrderByStmt = $this->Database->prepare($query);

			if (strlen($this->limit)) {
				$arrLimit = explode(',', $this->limit);
				$objOrderByStmt->limit($arrLimit[1], $arrLimit[0]);
			}

			$objOrderBy = $objOrderByStmt->execute($this->values);

			if ($objOrderBy->numRows < 1) {
				return $return . '
<p class="tl_empty_parent_view">' . $GLOBALS['TL_LANG']['MSC']['noResult'] . '</p>

</div>';
			}

			if (is_array($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_callback'])) {
				$strClass  = $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_callback'][0];
				$strMethod = $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_callback'][1];

				$this->import($strClass);
				$row      = $objOrderBy->fetchAllAssoc();
				$strGroup = '';

				// Make items sortable

				for ($i = 0; $i < count($row); $i++)
				{
					$this->current[] = $row[$i]['id'];
					$imagePasteAfter = $this->generateImage('pasteafter.gif', sprintf($GLOBALS['TL_LANG'][$this->strTable]['pasteafter'][1], $row[$i]['id']), 'class="blink"');
					$imagePasteNew   = $this->generateImage('new.gif', sprintf($GLOBALS['TL_LANG'][$this->strTable]['pastenew'][1], $row[$i]['id']));

					// Decrypt encrypted value
					foreach ($row[$i] as $k=> $v)
					{
						if ($GLOBALS['TL_DCA'][$table]['fields'][$k]['eval']['encrypt']) {
							$v = deserialize($v);

							$this->import('Encryption');
							$row[$i][$k] = $this->Encryption->decrypt($v);
						}
					}

					// Add the group header
					$sortingMode = (count($orderBy) == 1 && $firstOrderBy == $orderBy[0] && $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['flag'] != '' && $GLOBALS['TL_DCA'][$this->strTable]['fields'][$firstOrderBy]['flag'] == '') ? $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['flag'] : $GLOBALS['TL_DCA'][$this->strTable]['fields'][$firstOrderBy]['flag'];
					$group       = $this->formatGroupHeader('type', $row[$i]['type'], $sortingMode, $row[$i]);

					if ($group != $strGroup) {
						if ($strGroup) {
							$return .= "\n\n" . '</ul>

<script>
Backend.makeParentViewSortable("ul_' . CURRENT_ID . '_' . substr(md5($strGroup), 0, 8) . '");
</script>';
						}
						$return .= "\n\n" . '<div class="tl_content_header">' . $group . '</div>';
						$return .= "\n\n" . '<ul id="ul_' . CURRENT_ID . '_' . substr(md5($group), 0, 8) . '" class="sortable">';
						$strGroup = $group;
					}

					// Make items sortable
					if ($blnHasSorting) {
						$return .= '
<li id="li_' . $row[$i]['id'] . '">';
					}

					$return .= '

<div class="tl_content' . (($GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_class'] != '') ? ' ' . $GLOBALS['TL_DCA'][$this->strTable]['list']['sorting']['child_record_class'] : '') . '" onmouseover="Theme.hoverDiv(this,1)" onmouseout="Theme.hoverDiv(this,0)">
<div class="tl_content_right">';

					// Edit multiple
					if ($this->Input->get('act') == 'select') {
						$return .= '<input type="checkbox" name="IDS[]" id="ids_' . $row[$i]['id'] . '" class="tl_tree_checkbox" value="' . $row[$i]['id'] . '">';
					}

					// Regular buttons
					else
					{
						$return .= $this->generateButtons($row[$i], $this->strTable, $this->root, false, null, $row[($i - 1)]['id'], $row[($i + 1)]['id']);

						// Sortable table
						if ($blnHasSorting) {
							// Create new button
							if (!$GLOBALS['TL_DCA'][$this->strTable]['config']['closed']) {
								$return .= ' <a href="' . $this->addToUrl('act=create&amp;mode=1&amp;pid=' . $row[$i]['id'] . '&amp;id=' . $objParent->id) . '" title="' . specialchars(sprintf($GLOBALS['TL_LANG'][$this->strTable]['pastenew'][1], $row[$i]['id'])) . '">' . $imagePasteNew . '</a>';
							}

							// Prevent circular references
							if ($blnClipboard &&
								$arrClipboard['mode'] == 'cut' &&
								$row[$i]['id'] == $arrClipboard['id'] ||
								$blnMultiboard &&
									$arrClipboard['mode'] == 'cutAll' &&
									in_array($row[$i]['id'], $arrClipboard['id']) ||
								!$blnClipboard &&
									$blnGlobalClipboard ||
								($blnClipboard || $blnGlobalClipboard) &&
									!preg_match('#^' . preg_quote($this->Session->get('THEME_PLUS_FILE_TYPE')) . '#', $row[$i]['type'])
							) {
								$return .= ' ' . $this->generateImage('pasteafter_.gif', '', 'class="blink"');
							}

							// Copy/move multiple
							elseif ($blnClipboard && $blnMultiboard)
							{
								$return .= ' <a href="' . $this->addToUrl('act=' . $arrClipboard['mode'] . '&amp;mode=1&amp;pid=' . $row[$i]['id']) . '" title="' . specialchars(sprintf($GLOBALS['TL_LANG'][$this->strTable]['pasteafter'][1], $row[$i]['id'])) . '" onclick="Backend.getScrollOffset()">' . $imagePasteAfter . '</a>';
							}

							// Paste buttons
							elseif ($blnClipboard)
							{
								$return .= ' <a href="' . $this->addToUrl('act=' . $arrClipboard['mode'] . '&amp;mode=1&amp;pid=' . $row[$i]['id'] . '&amp;id=' . $arrClipboard['id']) . '" title="' . specialchars(sprintf($GLOBALS['TL_LANG'][$this->strTable]['pasteafter'][1], $row[$i]['id'])) . '" onclick="Backend.getScrollOffset()">' . $imagePasteAfter . '</a>';
							}
						}
					}

					$return .= '</div>' . $this->$strClass->$strMethod($row[$i]) . '</div>';

					// Make items sortable
					if ($blnHasSorting) {
						$return .= '

</li>';
					}
				}
			}
		}

		// Make items sortable
		if ($strGroup) {
			$return .= '
</ul>

<script>
Backend.makeParentViewSortable("ul_' . CURRENT_ID . '_' . substr(md5($strGroup), 0, 8) . '");
</script>';
		}

		$return .= '

</div>';

		// Close form
		if ($this->Input->get('act') == 'select') {
			$return .= '

<div class="tl_formbody_submit" style="text-align:right">

<div class="tl_submit_container">' . (!$GLOBALS['TL_DCA'][$this->strTable]['config']['notDeletable'] ? '
  <input type="submit" name="delete" id="delete" class="tl_submit" accesskey="d" onclick="return confirm(\'' . $GLOBALS['TL_LANG']['MSC']['delAllConfirm'] . '\')" value="' . specialchars($GLOBALS['TL_LANG']['MSC']['deleteSelected']) . '"> ' : '') . '
  <input type="submit" name="cut" id="cut" class="tl_submit" accesskey="x" value="' . specialchars($GLOBALS['TL_LANG']['MSC']['moveSelected']) . '">
  <input type="submit" name="copy" id="copy" class="tl_submit" accesskey="c" value="' . specialchars($GLOBALS['TL_LANG']['MSC']['copySelected']) . '"> ' . (!$GLOBALS['TL_DCA'][$this->strTable]['config']['notEditable'] ? '
  <input type="submit" name="override" id="override" class="tl_submit" accesskey="v" value="' . specialchars($GLOBALS['TL_LANG']['MSC']['overrideSelected']) . '">
  <input type="submit" name="edit" id="edit" class="tl_submit" accesskey="s" value="' . specialchars($GLOBALS['TL_LANG']['MSC']['editSelected']) . '"> ' : '') . '
</div>

</div>
</div>
</form>';
		}

		return $return;
	}

	protected function formatGroupHeader($field, $value, $mode, $row)
	{
		$type = preg_replace('#^(css|js)_.*$#', '$1', $value);

		switch ($type) {
			case 'css':
				return $GLOBALS['TL_LANG']['tl_theme_plus_file']['type_css'];

			case 'js':
				return $GLOBALS['TL_LANG']['tl_theme_plus_file']['type_js'];

			default:
				return '-';
		}
	}

	protected function getNewPosition($mode, $pid = null, $insertInto = false)
	{
		$objFile = $this->Database
			->prepare('SELECT * FROM tl_theme_plus_file WHERE id=?')
			->execute($this->intId);
		$strType = preg_replace('#^(css|js)_.*$#', '$1', $objFile->type);

		// PID is not set - only valid for duplicated records, as they get the same parent ID as the original record!
		if ($pid === null && $this->intId && $mode == 'copy') {
			$pid = $this->intId;
		}

		// PID is set (insert after or into the parent record)
		if (is_numeric($pid)) {
			// Insert the current record at the beginning when inserting into the parent record
			if ($insertInto) {
				$newPID     = $pid;
				$objSorting = $this->Database->prepare("SELECT MIN(sorting) AS sorting FROM " . $this->strTable . " WHERE type LIKE ? AND pid=?")
					->executeUncached($strType . '%', $pid);

				// Select sorting value of the first record
				if ($objSorting->numRows) {
					$curSorting = $objSorting->sorting;

					// Resort if the new sorting value is not an integer or smaller than 1
					if (($curSorting % 2) != 0 || $curSorting < 1) {
						$objNewSorting = $this->Database->prepare("SELECT id, sorting FROM " . $this->strTable . " WHERE type LIKE ? AND pid=? ORDER BY sorting")
							->executeUncached($strType . '%', $pid);

						$count      = 2;
						$newSorting = 128;

						while ($objNewSorting->next())
						{
							$this->Database->prepare("UPDATE " . $this->strTable . " SET sorting=? WHERE id=?")
								->limit(1)
								->execute(($count++ * 128), $objNewSorting->id);
						}
					}

					// Else new sorting = (current sorting / 2)
					else $newSorting = ($curSorting / 2);
				}

				// Else new sorting = 128
				else $newSorting = 128;
			}

			// Else insert the current record after the parent record
			elseif ($pid > 0)
			{
				$objSorting = $this->Database->prepare("SELECT * FROM " . $this->strTable . " WHERE type LIKE ? AND id=?")
					->limit(1)
					->executeUncached($strType . '%', $pid);

				// Set parent ID of the current record as new parent ID
				if ($objSorting->numRows) {
					$newPID     = $objSorting->pid;
					$curSorting = $objSorting->sorting;

					// Do not proceed without a parent ID
					if (is_numeric($newPID)) {
						$objNextSorting = $this->Database->prepare("SELECT MIN(sorting) AS sorting FROM " . $this->strTable . " WHERE type LIKE ? AND pid=? AND sorting>?")
							->executeUncached($strType . '%', $newPID, $curSorting);

						// Select sorting value of the next record
						if ($objNextSorting->sorting !== null) {
							$nxtSorting = $objNextSorting->sorting;

							// Resort if the new sorting value is no integer or bigger than a MySQL integer
							if ((($curSorting + $nxtSorting) % 2) != 0 || $nxtSorting >= 4294967295) {
								$count = 1;

								$objNewSorting = $this->Database->prepare("SELECT id, sorting FROM " . $this->strTable . " WHERE type LIKE ? AND pid=? ORDER BY sorting")
									->executeUncached($strType . '%', $newPID);

								while ($objNewSorting->next())
								{
									$this->Database->prepare("UPDATE " . $this->strTable . " SET sorting=? WHERE id=?")
										->execute(($count++ * 128), $objNewSorting->id);

									if ($objNewSorting->sorting == $curSorting) {
										$newSorting = ($count++ * 128);
									}
								}
							}

							// Else new sorting = (current sorting + next sorting) / 2
							else $newSorting = (($curSorting + $nxtSorting) / 2);
						}

						// Else new sorting = (current sorting + 128)
						else $newSorting = ($curSorting + 128);
					}
				}

				// Use the given parent ID as parent ID
				else
				{
					$newPID     = $pid;
					$newSorting = 128;
				}
			}

			// Set new sorting and new parent ID
			$this->set['pid']     = intval($newPID);
			$this->set['sorting'] = intval($newSorting);
		}
	}


}
