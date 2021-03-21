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

use PhpDocTypeReader\Type\FloatType;
use PhpDocTypeReader\Type\IntType;
use PhpDocTypeReader\Type\StringType;
use PHPUnit\Framework\TestCase;

class PhpDocTypeReaderTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testIsAbleToGetVarTypes($expected, string $doc_comment): void
    {
        $reader = new PhpDocTypeReader();
        $this->assertEquals($expected, $reader->getVarTypes($doc_comment));
    }

    public function provider(): array
    {
        return [
            [
                new IntType(),
                '/** @var int */'
            ],
            [
                new StringType(),
                '/** @var string */'
            ],
            [
                new FloatType(),
                '/** @var float */'
            ],
        ];
    }
}
