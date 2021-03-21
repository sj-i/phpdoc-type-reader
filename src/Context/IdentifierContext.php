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

final class IdentifierContext
{
    private string $namespace;
    /** @var array<string, string> */
    private array $aliases;

    /**
     * IdentifierContext constructor.
     * @param string $namespace
     * @param array<string, string> $aliases
     */
    public function __construct(string $namespace, array $aliases)
    {
        $this->namespace = $namespace;
        $this->aliases = $aliases;
    }

    public function getFqnFromContext(string $identifier): string
    {
        if (($identifier[0] ?? '') === '\\') {
            if ($identifier === '\\') {
                throw new \LogicException('invalid identifier');
            }
            return substr($identifier, 1);
        }
        if (isset($this->aliases[$identifier])) {
            return $this->aliases[$identifier];
        }
        return "{$this->namespace}\\{$identifier}";
    }
}
