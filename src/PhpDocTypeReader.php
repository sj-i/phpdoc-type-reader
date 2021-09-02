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

use PhpDocTypeReader\Context\IdentifierContext;
use PhpDocTypeReader\Type\ArrayKeyType;
use PhpDocTypeReader\Type\ArrayType;
use PhpDocTypeReader\Type\AtomicType;
use PhpDocTypeReader\Type\BoolType;
use PhpDocTypeReader\Type\FloatType;
use PhpDocTypeReader\Type\GenericType;
use PhpDocTypeReader\Type\IntType;
use PhpDocTypeReader\Type\MixedType;
use PhpDocTypeReader\Type\NullType;
use PhpDocTypeReader\Type\ObjectType;
use PhpDocTypeReader\Type\StringType;
use PhpDocTypeReader\Type\Type;
use PhpDocTypeReader\Type\UnionType;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
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

    public function getVarTypes(string $doc_comment, IdentifierContext $identifier_context): Type
    {
        $tokens = $this->lexer->tokenize($doc_comment);
        $token_iterator = new TokenIterator($tokens);
        $php_doc_node = $this->php_doc_parser->parse($token_iterator);
        $var_tag_values = $php_doc_node->getVarTagValues();

        if (count($var_tag_values) < 1) {
            throw new \LogicException('cannot find @var');
        }

        $var_tag = current($var_tag_values);
        return $this->getTypeFromNodeType($var_tag->type, $identifier_context);
    }

    /**
     * @return array<string, Type>
     */
    public function getParamTypes(string $doc_comment, IdentifierContext $identifier_context): array
    {
        $tokens = $this->lexer->tokenize($doc_comment);
        $token_iterator = new TokenIterator($tokens);
        $php_doc_node = $this->php_doc_parser->parse($token_iterator);
        $param_tag_values = $php_doc_node->getParamTagValues();

        if (count($param_tag_values) < 1) {
            throw new \LogicException('cannot find @param');
        }

        $result = [];
        foreach ($param_tag_values as $param_tag_value) {
            $result[ltrim($param_tag_value->parameterName, '$')] = $this->getTypeFromNodeType(
                $param_tag_value->type,
                $identifier_context
            );
        }
        return $result;
    }

    private function getTypeFromNodeType(TypeNode $type_node, IdentifierContext $identifier_context): Type
    {
        if ($type_node instanceof IdentifierTypeNode) {
            switch ($type_node->name) {
                case 'mixed':
                    return new MixedType();
                case 'array-key':
                    return new ArrayKeyType();
                case 'int':
                    return new IntType();
                case 'string':
                    return new StringType();
                case 'float':
                    return new FloatType();
                case 'bool':
                    return new BoolType();
                case 'null':
                    return new NullType();
                case 'array':
                    return new ArrayType(new MixedType());
                default:
                    return new ObjectType(
                        $this->tryGetClassNameFromIdentifier($type_node, $identifier_context)
                    );
            }
        }
        if ($type_node instanceof GenericTypeNode) {
            if ($type_node->type->name === 'array') {
                if (count($type_node->genericTypes) === 1) {
                    $type = $this->getTypeFromNodeType($type_node->genericTypes[0], $identifier_context);
                    return new ArrayType($type);
                } elseif (count($type_node->genericTypes) === 2) {
                    $key_type = $this->getTypeFromNodeType($type_node->genericTypes[0], $identifier_context);
                    $value_type = $this->getTypeFromNodeType($type_node->genericTypes[1], $identifier_context);
                    if (!($key_type instanceof ArrayKeyType)) {
                        throw new \LogicException('unsupported array key type');
                    }
                    return new ArrayType($value_type, $key_type);
                }
                throw new \LogicException('unsupported parameter types of array');
            }

            return new GenericType(
                new ObjectType($this->tryGetClassNameFromIdentifier($type_node->type, $identifier_context)),
                array_map(
                    fn ($type) => $this->getTypeFromNodeType($type, $identifier_context),
                    $type_node->genericTypes
                )
            );
        }
        if ($type_node instanceof ArrayTypeNode) {
            $type = $this->getTypeFromNodeType($type_node->type, $identifier_context);
            return new ArrayType($type);
        }
        if ($type_node instanceof UnionTypeNode) {
            $types = [];
            foreach ($type_node->types as $type) {
                $type = $this->getTypeFromNodeType($type, $identifier_context);
                if (!($type instanceof AtomicType)) {
                    throw new \LogicException('unsupported union type');
                }

                $types[] = $type;
            }

            return new UnionType($types);
        }
        /** @psalm-suppress ForbiddenCode */
        var_dump($type_node);
        throw new \LogicException('unsupported type');
    }



    private function tryGetClassNameFromIdentifier(
        IdentifierTypeNode $type,
        IdentifierContext $identifier_context
    ): string {
        return $identifier_context->getFqnFromContext($type->name);
    }
}
