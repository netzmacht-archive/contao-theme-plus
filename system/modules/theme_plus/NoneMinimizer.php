<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

#copyright

/**
 * Class NoneMinimizer
 *
 * wrapper class for the less css compiler (http://lesscss.org)
 * @copyright  InfinitySoft 2011
 * @author     Tristan Lins <tristan.lins@infinitysoft.de>
 * @package    Compression API
 */
class NoneMinimizer extends AbstractMinimizer
{
	/**
	 * (non-PHPdoc)
	 * @see Minimizer::minimizeCode($strCode)
	 */
	public function minimizeCode($strCode)
	{
		return $strCode;
	}
}
?>