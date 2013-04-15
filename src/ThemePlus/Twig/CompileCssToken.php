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

namespace ThemePlus\Twig;

use Twig_NodeInterface;
use Twig_Token;

class CompileCssToken extends \Twig_Node
{
	public function __construct($script, Twig_NodeInterface $arguments = null, $lineno = 0, $tag = null)
	{
		parent::__construct(array('script' => $script, 'arguments' => $arguments), array(), $lineno, $tag);
	}

	public function compile(\Twig_Compiler $compiler)
	{
        $compiler
            ->addDebugInfo($this)
            ->write("ob_start();\n")
            ->subcompile($this->getNode('script'))
            ->write("\$script = ob_get_clean();\n")
			->write("\$arguments = ")
			->subcompile($this->getNode('arguments'))
			->raw(";\n")
			->write("echo \\ThemePlus\\TwigExtension::compileJs(\n")
			->indent()
			->write("\$script,\n")
			->write("isset(\$arguments['filter']) ? \$arguments['filter'] : null,\n")
			->write("isset(\$arguments['debug']) ? \$arguments['debug'] : null\n")
			->outdent()
			->write(");\n")
		;
	}
}