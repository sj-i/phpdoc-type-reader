<?php

/**
 * This file is part of the sj-i/phpdoc-type-reader package.
 *
 * (c) sji <sji@sj-i.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PhpDocTypeReader;

use PhpDocTypeReader\Type\BoolType;
use PhpDocTypeReader\Type\FloatType;
use PhpDocTypeReader\Type\IntType;
use PhpDocTypeReader\Type\StringType;
use PhpDocTypeReader\Type\Type;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;

final class PhpDocTypeReader
{
    private PhpDocParser $php_doc_parser;
    private Lexer $lexer;

    public function __construct(
        ?PhpDocParser $php_doc_parser = null,
        ?Lexer $lexer = null
    ) {
        if (is_null($php_doc_parser)) {
            $const_expr_parser = new ConstExprParser();
            $php_doc_parser = new PhpDocParser(
                new TypeParser($const_expr_parser),
                $const_expr_parser
            );
        }
        if (is_null($lexer)) {
            $lexer = new Lexer();
        }
        $this->php_doc_parser = $php_doc_parser;
        $this->lexer = $lexer;
    }

    public function getVarTypes(string $doc_comment): Type
    {
        $tokens = $this->lexer->tokenize($doc_comment);
        $token_iterator = new TokenIterator($tokens);
        $php_doc_node = $this->php_doc_parser->parse($token_iterator);
        $var_tag_values = $php_doc_node->getVarTagValues();

        if (count($var_tag_values) < 1) {
            throw new \LogicException('cannot find @var');
        }

        $var_tag = current($var_tag_values);
        if ($var_tag->type instanceof IdentifierTypeNode) {
            switch ($var_tag->type->name) {
                case 'int':
                    return new IntType();
                case 'string':
                    return new StringType();
                case 'float':
                    return new FloatType();
                case 'bool':
                    return new BoolType();
            }
        }
    }
}
