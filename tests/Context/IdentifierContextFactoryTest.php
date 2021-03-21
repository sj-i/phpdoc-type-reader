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

class IdentifierContextFactoryTest extends TestCase
{
    public function testCreateFromFile(): void
    {
        $factory = new IdentifierContextFactory();
        $context = $factory->createFromFile(__FILE__);
        $this->assertSame(TestCase::class, $context->getFqnFromContext('TestCase'));
        $this->assertSame(IdentifierContext::class, $context->getFqnFromContext('IdentifierContext'));
    }
}
