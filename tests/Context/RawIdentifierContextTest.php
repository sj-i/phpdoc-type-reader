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

namespace PhpDocTypeReader\Context;

use PHPUnit\Framework\TestCase;

class RawIdentifierContextTest extends TestCase
{
    /**
     * @dataProvider provider
     */
    public function testGetFqdnFromContext(
        string $expected,
        string $identifier,
        string $namespace,
        array $aliases
    ): void {
        $identifier_context = new RawIdentifierContext($namespace, $aliases);
        $this->assertSame($expected, $identifier_context->getFqnFromContext($identifier));
    }

    public function provider()
    {
        return [
            [
                IdentifierContext::class,
                'IdentifierContext',
                __NAMESPACE__,
                [],
            ],
            [
                '\\Aliased',
                'Alias',
                '\\',
                [
                    'Alias' => '\\Aliased'
                ],
            ],
        ];
    }
}
