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

use PhpParser\NameContext;
use PhpParser\Node\Name;

final class PhpParserIdentifierContext implements IdentifierContext
{
    private NameContext $name_context;

    public function __construct(NameContext $name_context)
    {
        $this->name_context = $name_context;
    }
    public function getFqnFromContext(string $identifier): string
    {
        return $this->name_context->getResolvedClassName(new Name($identifier))->toString();
    }
}