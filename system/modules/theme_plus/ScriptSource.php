<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright

/**
 * Class ScriptSource
 *
 * Front end content element "script_source".
 * @copyright  InfinitySoft 2010,2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Layout Additional Sources
 */
class ScriptSource extends Frontend
{
	/**
	 * Current record
	 * @var array
	 */
	protected $arrData = array();
	
	
	/**
	 * Initialize the object
	 * @param object
	 * @return string
	 */
	public function __construct(Database_Result $objElement)
	{
		parent::__construct();

		$this->arrData = $objElement->row();
	}


	/**
	 * Set an object property
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue)
	{
		$this->arrData[$strKey] = $varValue;
	}


	/**
	 * Return an object property
	 * @param string
	 * @return mixed
	 */
	public function __get($strKey)
	{
		return $this->arrData[$strKey];
	}

	
	/**
	 * Generate frontend element
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$this->import('Database');
			$arrScriptSource = deserialize($this->script_source);
			if (count($arrScriptSource))
			{
				$objSource = $this->Database->execute("SELECT * FROM tl_additional_source WHERE id IN (" . implode(',', array_map('intval', $arrScriptSource)) . ")");
				$strBuffer = '';
				while ($objSource->next())
				{
					$strType = $objSource->type;
					$label = ' ' . $objSource->$strType;
					
					if (strlen($objSource->cc)) {
						$label .= ' <span style="color: #B3B3B3;">[' . $objSource->cc . ']</span>';
					}
					
					if (strlen($objSource->media)) {
						$arrMedia = unserialize($objSource->media);
						if (count($arrMedia)) {
							$label .= ' <span style="color: #B3B3B3;">[' . implode(', ', $arrMedia) . ']</span>';
						}
					}
					
					$strBuffer .= $this->generateImage('iconJS.gif', $label, 'style="vertical-align:middle"') . $label . '<br/>';
				}
				return $strBuffer;
			}
			return $GLOBALS['TL_LANG']['MSC']['noResult'];
		}
		
		$this->import('LayoutAdditionalSources');
		return implode("\n", $this->LayoutAdditionalSources->generateIncludeHtml(deserialize($this->script_source, true))) . "\n";
	}
}

?>