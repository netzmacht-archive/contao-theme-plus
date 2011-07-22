<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright


/**
 * Class LocalThemePlusFile
 */
abstract class LocalThemePlusFile extends ThemePlusFile
{
	/**
	 * Get a file from path. 
	 */
	public static function create()
	{
		$args = func_get_args();
		$strFile = $args[0];
		if (file_exists(TL_ROOT . '/' . $strFile))
		{
			$objFile = new File($strFile);
			switch ($objFile->extension)
			{
				case 'js':
					return new LocalJavaScriptFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : false);
				
				case 'css':
					if (!$GLOBALS['TL_CONFIG']['theme_plus_force_less'])
					{
						return new LocalCssFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : '', isset($args[3]) ? $args[3] : false, isset($args[4]) ? $args[4] : false);
					}
					
				case 'less':
					return new LocalLessCssFile($strFile, isset($args[1]) ? $args[1] : '', isset($args[2]) ? $args[2] : '', isset($args[3]) ? $args[3] : false, isset($args[4]) ? $args[4] : false);
			}
		}
		return false;
	}
	

	/**
	 * The origin file path.
	 */
	protected $strOriginFile;
	
	
	/**
	 * The corresponding theme.
	 */
	protected $objTheme;
	
	
	/**
	 * Create a new local file object.
	 */
	public function __construct($strOriginFile, $strCc = '', $objTheme = false)
	{
		if (!file_exists(TL_ROOT . '/' . $strOriginFile))
		{
			throw new Exception('File does not exists: ' . $strOriginFile);
		}
		
		parent::__construct($strCc);
		$this->strOriginFile = $strOriginFile;
		$this->objTheme = $objTheme;
	}
	
	
	/**
	 * Get the original file path relative to TL_ROOT.
	 */
	public function getOriginFile()
	{
		return $this->strOriginFile;
	}
	
	
	/**
	 * Get the file path relative to TL_ROOT
	 */
	public abstract function getFile();
	
	
	public function getGlobalVariableCode()
	{
		return $this->getFile() . (strlen($this->strCc) ? '|' . $this->strCc : '');
	}
	
		
	public function __get($k)
	{
		switch ($k)
		{
		case 'origin':
			return $this->getOriginFile();
			
		case 'file':
		case 'path':
			return $this->getFile();
		
		default:
			return parent::__get($k);
		}
	}
	
}

?>