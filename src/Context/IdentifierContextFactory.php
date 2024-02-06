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

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PhpParser\PhpVersion;

final class IdentifierContextFactory
{
    public function createFromPhpCode(string $php_code): IdentifierContext
    {
        $parser = (new ParserFactory())->createForVersion(
            PhpVersion::getHostVersion()
        );
        $statements = $parser->parse($php_code);
        if (is_null($statements)) {
            throw new \LogicException('parse error');
        }
        $traverser = new NodeTraverser();
        $name_resolver = new NameResolver();
        $traverser->addVisitor($name_resolver);
        $traverser->traverse($statements);
        $name_context = $name_resolver->getNameContext();
        return new PhpParserIdentifierContext($name_context);
    }

    public function createFromFile(string $file_name): IdentifierContext
    {
        return $this->createFromPhpCode(file_get_contents($file_name));
    }
}
