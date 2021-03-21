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
use PhpDocTypeReader\Type\BoolType;
use PhpDocTypeReader\Type\FloatType;
use PhpDocTypeReader\Type\GenericType;
use PhpDocTypeReader\Type\IntType;
use PhpDocTypeReader\Type\ObjectType;
use PhpDocTypeReader\Type\StringType;
use PHPUnit\Framework\TestCase;

class PhpDocTypeReaderTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testIsAbleToGetVarTypes($expected, string $doc_comment, IdentifierContext $identifier_context): void
    {
        $reader = new PhpDocTypeReader();
        $this->assertEquals($expected, $reader->getVarTypes($doc_comment, $identifier_context));
    }

    public function provider(): array
    {
        $default_identifier_context = new IdentifierContext(
            __NAMESPACE__,
            []
        );
        return [
            [
                new IntType(),
                '/** @var int */',
                $default_identifier_context
            ],
            [
                new StringType(),
                '/** @var string */',
                $default_identifier_context
            ],
            [
                new FloatType(),
                '/** @var float */',
                $default_identifier_context
            ],
            [
                new BoolType(),
                '/** @var bool */',
                $default_identifier_context
            ],
            [
                new ObjectType(PhpDocTypeReader::class),
                '/** @var PhpDocTypeReader */',
                $default_identifier_context
            ],
            [
                new GenericType(
                    new ObjectType(\Iterator::class),
                    [
                        new IntType(),
                        new ObjectType(PhpDocTypeReader::class)
                    ]
                ),
                '/** @var \Iterator<int, PhpDocTypeReader> */',
                $default_identifier_context
            ],
        ];
    }
}
