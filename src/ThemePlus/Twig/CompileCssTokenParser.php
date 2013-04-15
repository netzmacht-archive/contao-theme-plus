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

class CompileCssTokenParser extends \Twig_TokenParser
{
	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_NodeInterface A Twig_NodeInterface instance
	 */
	public function parse(Twig_Token $token)
	{
		$lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $arguments = null;
        if ($stream->test(Twig_Token::NAME_TYPE, 'with')) {
            $stream->next();

            $arguments = $this->parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(Twig_Token::BLOCK_END_TYPE);
        $this->parser->pushLocalScope();
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $this->parser->popLocalScope();
        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new CompileJsToken($body, $arguments, $lineno, $this->getTag());
	}

    public function decideBlockEnd(Twig_Token $token)
    {
        return $token->test('endcompilecss');
    }

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'compilecss';
	}
}